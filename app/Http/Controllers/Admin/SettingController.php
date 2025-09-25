<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\ServiceFee;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $setting = AdminSetting::query()->first();
        $serviceFees = ServiceFee::ordered()->get();
        return view('admin.settings', compact('setting', 'serviceFees'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'language' => 'required|in:uz,ru,en',
            'cny_to_uzs' => 'nullable|numeric|min:0',
        ]);

        $setting = AdminSetting::query()->first();
        if (!$setting) {
            $setting = new AdminSetting();
        }
        $setting->fill($data)->save();

        return back()->with('status', 'Sozlamalar saqlandi');
    }

    public function storeServiceFee(Request $request)
    {
        $data = $request->validate([
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|min:0|gte:min_amount',
            'fee_percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        // Faol qoidalar sonini tekshirish
        $activeCount = ServiceFee::where('is_active', true)->count();
        if ($data['is_active'] && $activeCount >= 10) {
            return back()->withErrors(['error' => 'Maksimal 10 ta faol xizmat haqi qoidasi bo\'lishi mumkin']);
        }

        ServiceFee::create($data);

        return back()->with('status', 'Xizmat haqi qoidasi qo\'shildi');
    }

    public function updateServiceFee(Request $request, ServiceFee $serviceFee)
    {
        $data = $request->validate([
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|min:0|gte:min_amount',
            'fee_percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        // Faol qoidalar sonini tekshirish
        if ($data['is_active']) {
            $activeCount = ServiceFee::where('is_active', true)->where('id', '!=', $serviceFee->id)->count();
            if ($activeCount >= 10) {
                return back()->withErrors(['error' => 'Maksimal 10 ta faol xizmat haqi qoidasi bo\'lishi mumkin']);
            }
        }

        $serviceFee->update($data);

        return back()->with('status', 'Xizmat haqi qoidasi yangilandi');
    }

    public function destroyServiceFee(ServiceFee $serviceFee)
    {
        $serviceFee->delete();
        return back()->with('status', 'Xizmat haqi qoidasi o\'chirildi');
    }
}



