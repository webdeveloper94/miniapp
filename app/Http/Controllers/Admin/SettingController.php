<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $setting = AdminSetting::query()->first();
        return view('admin.settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'language' => 'required|in:uz,ru,en',
            'service_fee_percent' => 'required|numeric|min:0|max:100',
        ]);

        $setting = AdminSetting::query()->first();
        if (!$setting) {
            $setting = new AdminSetting();
        }
        $setting->fill($data)->save();

        return back()->with('status', 'Sozlamalar saqlandi');
    }
}



