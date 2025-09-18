<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $query = User::query();

        if (request('q')) {
            $q = trim(request('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('username', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        if (request('role')) {
            $query->where('role', request('role'));
        }

        $users = $query->orderByDesc('id')->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users'));
    }
}


