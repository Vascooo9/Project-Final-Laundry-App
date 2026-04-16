<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'user', 'transaction'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        $statusCounts = [
            'all'        => Order::count(),
            'pending'    => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'done'       => Order::where('status', 'done')->count(),
            'picked_up'  => Order::where('status', 'picked_up')->count(),
        ];

        return view('orders.index', compact('orders', 'statusCounts'));
    }

    public function create()
    {
        $services = Service::where('is_active', true)->get();

        $servicesFormatted = $services->map(fn($s) => [
            'id'    => $s->id,
            'price' => (float) $s->price,
            'type'  => $s->type,
            'label' => $s->name . ' - Rp ' . number_format($s->price, 0, ',', '.') . ($s->type === 'per_kg' ? '/kg' : '/item'),
        ]);

        return view('orders.create', [
            'services' => $servicesFormatted
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'    => ['required', 'string', 'max:255'],
            'customer_phone'   => ['nullable', 'string', 'max:20'],
            'delivery_type'    => ['required', 'in:pickup,delivery'],
            'delivery_address' => ['required_if:delivery_type,delivery', 'nullable', 'string'],
            'delivery_phone'   => ['required_if:delivery_type,delivery', 'nullable', 'string'],
            'estimated_done'   => ['required', 'date', 'after_or_equal:today'],
            'notes'            => ['nullable', 'string'],
            'services'         => ['required', 'array', 'min:1'],
            'services.*.id'    => ['required', 'exists:services,id'],
            'services.*.qty'   => ['required', 'numeric', 'min:0.1'],
            'services.*.note'  => ['nullable', 'string'],
        ]);

        // ✅ FIX: Deklarasi $order di luar closure agar bisa diakses setelah transaksi
        $order = null;

        DB::transaction(function () use ($request, &$order) {
            $customer = Customer::firstOrCreate(
                ['name' => $request->customer_name],
                [
                    'phone'   => $request->customer_phone,
                    'address' => $request->delivery_address,
                ]
            );

            $order = Order::create([
                'customer_id'      => $customer->id,
                'user_id'          => Auth::id(),
                'delivery_type'    => $request->delivery_type,
                'delivery_address' => $request->delivery_address,
                'delivery_phone'   => $request->delivery_phone,
                'estimated_done'   => $request->estimated_done,
                'notes'            => $request->notes,
                'status'           => 'pending',
                'payment_status'   => 'unpaid',
            ]);

            $total = 0;
            foreach ($request->services as $serviceData) {
                $service  = Service::find($serviceData['id']);
                $qty      = floatval($serviceData['qty']);
                $subtotal = $service->price * $qty;
                $total   += $subtotal;

                OrderItem::create([
                    'order_id'       => $order->id,
                    'service_id'     => $service->id,
                    'quantity'       => $qty,
                    'price_per_unit' => $service->price,
                    'subtotal'       => $subtotal,
                    'item_note'      => $serviceData['note'] ?? null,
                ]);
            }

            $order->update(['total_amount' => $total]);
        });

        // ✅ FIX: Redirect langsung pakai $order (tidak lagi pakai session)
        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order berhasil dibuat! Silakan proses pembayaran.');
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items.service', 'user', 'transaction.receiver']);
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'in:pending,processing,done,picked_up'],
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'picked_up') {
            $data['picked_up_at'] = now();
        }

        $order->update($data);

        return back()->with('success', 'Status order berhasil diubah.');
    }

    public function processPayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_method'   => ['required', 'in:cash,transfer'],
            'reference_number' => ['required_if:payment_method,transfer', 'nullable', 'string'],
        ]);

        if ($order->payment_status === 'paid') {
            return back()->with('error', 'Order ini sudah dibayar.');
        }

        DB::transaction(function () use ($request, $order) {
            Transaction::create([
                'order_id'         => $order->id,
                'amount'           => $order->total_amount,
                'payment_method'   => $request->payment_method,
                'reference_number' => $request->reference_number,
                'paid_at'          => now(),
                'received_by'      => Auth::id(),
            ]);

            $order->update([
                'payment_status' => 'paid',
                'payment_method' => $request->payment_method,
                'status'         => 'processing',
            ]);
        });

        return redirect()->route('orders.show', $order)->with('success', 'Pembayaran berhasil dicatat!');
    }

    public function receipt(Order $order)
    {
        $order->load(['customer', 'items.service', 'user', 'transaction']);
        return view('orders.receipt', compact('order'));
    }
}
