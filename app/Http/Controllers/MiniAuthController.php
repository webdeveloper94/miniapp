<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MiniAuthController extends Controller
{
    public function setPassword(Request $request)
    {
        $data = $request->validate([
            'password' => 'required|string|min:4|max:50',
        ]);

        $telegramUser = session('telegram_user');
        if (!$telegramUser) {
            return back()->withErrors(['error' => 'Telegram foydalanuvchi topilmadi']);
        }

        $userId = $telegramUser['id'];
        $user = User::findOrFail($userId);
        $user->login_password = $data['password'];
        $user->save();

        return back()->with('status', 'Parol saqlandi');
    }

    public function recover(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|min:3|max:50',
            'password' => 'required|string|min:4|max:50',
        ]);

        $user = User::where('username', $data['username'])->first();
        if (!$user || !Hash::check($data['password'], (string) $user->login_password)) {
            return back()->withErrors(['recover' => 'Username yoki parol noto\'g\'ri']);
        }

        // Re-bind Telegram session to recovered profile
        $telegramUser = session('telegram_user', []);
        $telegramUser['id'] = $user->id;
        $telegramUser['username'] = $user->username;
        session(['telegram_user' => $telegramUser]);

        return redirect()->route('mini.home')->with('status', 'Profil tiklandi');
    }

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'old_password' => 'nullable|string|min:4|max:50',
            'new_password' => 'required|string|min:4|max:50|confirmed',
        ]);

        $telegramUser = session('telegram_user');
        if (!$telegramUser) {
            return back()->withErrors(['error' => 'Telegram foydalanuvchi topilmadi']);
        }

        $user = User::findOrFail($telegramUser['id']);

        if ($user->login_password) {
            if (!$data['old_password'] || !Hash::check($data['old_password'], (string) $user->login_password)) {
                return back()->withErrors(['old_password' => 'Eski parol noto\'g\'ri']);
            }
        }

        $user->login_password = $data['new_password'];
        $user->save();

        return back()->with('status', 'Parol yangilandi');
    }
}


