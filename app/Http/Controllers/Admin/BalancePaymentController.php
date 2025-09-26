<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class BalancePaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::where('type', 'balance_topup')
            ->with('user')
            ->latest()
            ->paginate(20);
            
        return view('admin.balance-payments.index', compact('payments'));
    }

    public function approve(Request $request, Payment $payment)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500'
        ]);

        if ($payment->type !== 'balance_topup') {
            return back()->withErrors(['error' => 'Bu balans to\'lovi emas']);
        }

        if ($payment->status !== 'pending') {
            return back()->withErrors(['error' => 'Bu to\'lov allaqachon ko\'rib chiqilgan']);
        }

        // To'lovni tasdiqlash
        $payment->status = 'approved';
        $payment->note = $request->admin_note;
        $payment->save();

        // User balansini oshirish
        $user = $payment->user;
        $user->balance += $payment->amount;
        $user->save();

        return back()->with('success', 'Balans to\'lovi tasdiqlandi va foydalanuvchi balansi oshirildi!');
    }

    public function reject(Request $request, Payment $payment)
    {
        $request->validate([
            'admin_note' => 'required|string|max:500'
        ]);

        if ($payment->type !== 'balance_topup') {
            return back()->withErrors(['error' => 'Bu balans to\'lovi emas']);
        }

        if ($payment->status !== 'pending') {
            return back()->withErrors(['error' => 'Bu to\'lov allaqachon ko\'rib chiqilgan']);
        }

        // To'lovni rad etish
        $payment->status = 'rejected';
        $payment->note = $request->admin_note;
        $payment->save();

        return back()->with('success', 'Balans to\'lovi rad etildi!');
    }
}