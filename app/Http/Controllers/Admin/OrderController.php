<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $query = Order::query()->with('user');

        if (request('q')) {
            $q = trim(request('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('id', $q)
                    ->orWhere('tracking_number', 'like', "%{$q}%")
                    ->orWhere('product_url', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        $orders = $query->orderByDesc('id')->paginate(20)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,accepted,rejected,shipping,delivered,cancelled',
            'tracking_number' => 'nullable|string',
        ]);
        $order->update($data);
        return back()->with('status', 'Holat yangilandi');
    }
}


