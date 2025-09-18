<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $recentOrders = Order::query()->with('user')->orderByDesc('id')->limit(5)->get();
        $recentPayments = Payment::query()->with(['user','order'])->orderByDesc('id')->limit(5)->get();
        $stats = [
            'orders_count' => Order::count(),
            'payments_pending' => Payment::where('status', 'pending')->count(),
            'orders_pending' => Order::where('status', 'pending')->count(),
        ];

        return view('admin.dashboard', compact('recentOrders', 'recentPayments', 'stats'));
    }
}


