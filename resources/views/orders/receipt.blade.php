<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota - {{ $order->order_number }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        .total-row {
            font-size: 14px;
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 4px;
            margin-top: 4px;
        }

        .small {
            font-size: 10px;
        }

        @media print {
            body {
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="center bold" style="font-size:16px">Vasco Laundry</div>
    <div class="center small">Sistem Laundry Profesional</div>
    <div class="divider"></div>

    <div class="center bold">{{ $order->order_number }}</div>
    <div class="center small">{{ $order->created_at->format('d/m/Y H:i') }}</div>

    <div class="divider"></div>

    <div class="row"><span>Customer</span><span class="bold">{{ $order->customer->name }}</span></div>
    @if($order->customer->phone)
        <div class="row"><span>HP</span><span>{{ $order->customer->phone }}</span></div>
    @endif
    <div class="row"><span>Pengambilan</span><span>{{ $order->delivery_type_label }}</span></div>
    <div class="row"><span>Est. Selesai</span><span>{{ $order->estimated_done->format('d/m/Y') }}</span></div>

    @if($order->delivery_type === 'delivery')
        <div class="divider"></div>
        <div><span class="bold">Alamat Kirim:</span></div>
        <div>{{ $order->delivery_address }}</div>
        <div>📱 {{ $order->delivery_phone }}</div>
    @endif

    <div class="divider"></div>
    <div class="center bold">DETAIL LAYANAN</div>
    <div class="divider"></div>

    @foreach($order->items as $item)
        <div class="bold">{{ $item->service->name }}</div>
        @if($item->item_note)
        <div class="small">>> {{ $item->item_note }}</div>@endif
        <div class="row">
            <span>
                {{ $item->quantity }} {{ $item->service->type === 'per_kg' ? 'kg' : 'item' }}
                x Rp {{ number_format($item->price_per_unit, 0, ',', '.') }}
            </span>
            <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
    @endforeach

    @php
        $subtotal = $order->subtotal ?? 0;
        $memberDiscount = $order->discount_amount ?? 0;
        $voucherDiscount = $order->transaction->voucher_discount ?? 0;

        $afterMember = $subtotal - $memberDiscount;
        $afterVoucher = $afterMember - $voucherDiscount;
        $deliveryFee = $order->delivery_fee ?? 0;
        $tax = $order->transaction->tax_amount ?? 0;
        $grandTotal = $afterVoucher + $tax + $deliveryFee;
    @endphp

    <div class="divider"></div>

    <div class="row">
        <span>Subtotal</span>
        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
    </div>

    @if($memberDiscount > 0)
        <div class="row">
            <span>Diskon</span>
            <span>- Rp {{ number_format($memberDiscount, 0, ',', '.') }}</span>
        </div>
    @endif

    @if($order->transaction?->voucher_discount > 0)
        <div class="row">
            <span>Voucher ({{ $order->transaction->voucher_code }})</span>
            <span>- Rp {{ number_format($order->transaction->voucher_discount, 0, ',', '.') }}</span>
        </div>
    @endif

    <div class="row">
        <span>Tax (10%)</span>
        <span>Rp {{ number_format($tax, 0, ',', '.') }}</span>
    </div>

    @if(($order->delivery_fee ?? 0) > 0)
        <div class="row">
            <span>Ongkir</span>
            <span>Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</span>
        </div>
    @endif

    <div class="row total-row">
        <span>TOTAL</span>
        <span>Rp {{ number_format($order->transaction->amount, 0, ',', '.') }}</span>
    </div>

    @if($order->payment_status === 'paid')
        <div class="divider"></div>
        <div class="row"><span>Status Bayar</span><span class="bold">✅ LUNAS</span></div>
        <div class="row"><span>Metode</span><span>{{ ucfirst($order->payment_method) }}</span></div>
        @if($order->transaction?->reference_number)
            <div class="row small"><span>Ref</span><span>{{ $order->transaction->reference_number }}</span></div>
        @endif
        @if($order->payment_method === 'cash' && $order->transaction)
            <div class="row"><span>Dibayar</span><span>Rp
                    {{ number_format($order->transaction->cash_received, 0, ',', '.') }}</span></div>
            <div class="row"><span>Kembalian</span><span>Rp
                    {{ number_format($order->transaction->change_amount, 0, ',', '.') }}</span></div>
        @endif
    @else
        <div class="divider"></div>
        <div class="row"><span>Status Bayar</span><span class="bold">⏳ BELUM BAYAR</span></div>
    @endif

    <div class="divider"></div>
    <div class="center small">Terima kasih telah menggunakan</div>
    <div class="center small bold">Vasco Laundry</div>
    <div class="center small">Barang ditunggu paling lambat 30 hari</div>

    <div style="margin-top:15px;" class="no-print center">
        <button onclick="window.print()"
            style="padding:8px 20px; background:#0369a1; color:white; border:none; border-radius:8px; cursor:pointer; font-size:13px;">
            🖨️ Cetak Nota
        </button>
        <button onclick="window.close()"
            style="padding:8px 20px; background:#e5e7eb; color:#374151; border:none; border-radius:8px; cursor:pointer; font-size:13px; margin-left:8px;">
            ✕ Tutup
        </button>
    </div>
</body>

</html>