<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::latest()->paginate(15);
        return view('admin.services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'type'        => ['required', 'in:per_kg,per_item'],
            'price'       => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        Service::create($request->only('name', 'type', 'price', 'description'));
        return back()->with('success', 'Layanan berhasil ditambahkan!');
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'type'        => ['required', 'in:per_kg,per_item'],
            'price'       => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['boolean'],
        ]);

        $service->update($request->only('name', 'type', 'price', 'description', 'is_active'));
        return back()->with('success', 'Layanan berhasil diperbarui!');
    }

    public function destroy(Service $service)
    {
        $service->update(['is_active' => false]);
        return back()->with('success', 'Layanan dinonaktifkan.');
    }
}