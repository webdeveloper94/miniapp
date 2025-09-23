<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $recentOrders = Order::query()->with('user')->orderByDesc('id')->limit(10)->get();
        $pendingOrders = Order::query()->with('user')->where('status', 'pending')->orderByDesc('id')->limit(5)->get();
        $recentPayments = Payment::query()->with(['user','order'])->orderByDesc('id')->limit(10)->get();

        $stats = [
            'orders_count' => Order::count(),
            'payments_pending' => Payment::where('status', 'pending')->count(),
            'orders_pending' => Order::where('status', 'pending')->count(),
            'orders_paid' => Payment::where('status', 'approved')->count(),
            'total_visitor' => Order::distinct('user_id')->count('user_id'),
            'total_sales' => (float) Payment::where('status', 'approved')->sum('amount'),
            'total_income' => (float) Payment::where('status', 'approved')->sum('amount'),
        ];

        // Recent orders by month for chart (last 12 months)
        $ordersByMonth = Order::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as c")
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->pluck('c', 'ym');

        $chartLabels = [];
        $chartSeries = [];
        $start = now()->subMonths(11)->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $key = $start->copy()->addMonths($i)->format('Y-m');
            $chartLabels[] = $start->copy()->addMonths($i)->format('M');
            $chartSeries[] = (int) ($ordersByMonth[$key] ?? 0);
        }

        return view('admin.dashboard', compact('recentOrders', 'recentPayments', 'stats', 'chartLabels', 'chartSeries', 'pendingOrders'));
    }
}


