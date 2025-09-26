<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $query = Payment::query()->with(['user','order']);

        if (request('q')) {
            $q = trim(request('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('id', $q)
                    ->orWhere('amount', $q)
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        $payments = $query->orderByDesc('id')->paginate(20)->withQueryString();
        return view('admin.payments.index', compact('payments'));
    }

    public function approve(Payment $payment)
    {
        // Only act if still pending
        if ($payment->status !== 'approved') {
            $payment->update(['status' => 'approved']);

            // If this is a balance top-up, credit user's balance
            if ($payment->type === 'balance_topup' && $payment->user) {
                try {
                    $payment->user->increment('balance', $payment->amount);
                } catch (\Throwable $e) {
                    \Log::error('Balance credit failed on approval', [
                        'payment_id' => $payment->id,
                        'user_id' => $payment->user_id,
                        'amount' => $payment->amount,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return back()->with('status', 'To\'lov tasdiqlandi');
    }

    public function reject(Payment $payment, Request $request)
    {
        $data = $request->validate(['note' => 'nullable|string']);
        $payment->update(['status' => 'rejected', 'note' => $data['note'] ?? null]);
        return back()->with('status', 'To\'lov rad etildi');
    }

    public function show(Payment $payment)
    {
        $payment->load(['user', 'order.orderItems']);
        return view('admin.payments.show', compact('payment'));
    }
}


