<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Services\DajiSaasClient;

class UserAppController extends Controller
{
    public function home()
    {
        return view('mini.home');
    }

    public function findProduct(Request $request, DajiSaasClient $api)
    {
        $data = $request->validate(['link' => 'required|string']);
        $link = $data['link'];
        // Detect platform and fetch accordingly
        $offerIdForSession = null;
        if (preg_match('~(1688\.com|offer/)~i', $link)) {
            // Extract offerId from 1688 mobile URL
            preg_match('~(offerId|item_id|object_id)=(\d+)~', $link, $m);
            $offerId = $m[2] ?? null;
            if (!$offerId && preg_match('~/offer/(\d+)~', $link, $m2)) {
                $offerId = $m2[1];
            }
            $offerIdForSession = $offerId;
            if ($offerId) {
                // Preferred per provided docs: queryProductDetail (GET)
                $product = $api->alibabaQueryProductDetail($offerId);
                if (isset($product['_error'])) {
                    // Fallback chain: detail by offerId (POST), then shortUrl (POST)
                    $product = $api->alibabaProductDetailByOfferId($offerId);
                    if (isset($product['_error'])) {
                        $product = $api->alibabaProductDetailByShortUrl($link);
                    }
                }
            } else {
                // Fallback: some 1688 short/mobile links may require shortUrl endpoint
                $product = $api->alibabaProductDetailByShortUrl($link);
            }
        } else {
            $product = $api->taobaoProductDetail($link);
        }
        if (isset($product['_error'])) {
            return back()->withErrors(['api' => 'API xatosi: '.$product['status'].' '.$product['endpoint']]);
        }
        // Persist product in session for redirect safety
        session(['mini_product' => $product, 'mini_product_link' => $link, 'mini_offerId' => $offerIdForSession]);
        return redirect()->route('mini.product');
    }

    public function productPage()
    {
        $product = session('mini_product');
        $link = session('mini_product_link');
        $offerId = session('mini_offerId');
        abort_unless($product, 404);
        return view('mini.product', compact('product', 'link', 'offerId'));
    }

    public function orders()
    {
        $orders = Order::query()->latest('id')->limit(10)->get();
        return view('mini.orders', compact('orders'));
    }

    public function cart()
    {
        $items = [];
        return view('mini.cart', compact('items'));
    }

    public function profile()
    {
        $payments = Payment::query()->latest('id')->limit(5)->get();
        return view('mini.profile', compact('payments'));
    }

    public function history()
    {
        $orders = Order::query()->latest('id')->paginate(10);
        return view('mini.history', compact('orders'));
    }

    public function freightEstimate(Request $request, DajiSaasClient $api)
    {
        $data = $request->validate([
            'offerId' => 'required|digits_between:6,20',
            'toProvinceCode' => 'nullable|string',
            'toCityCode' => 'nullable|string',
            'toCountryCode' => 'nullable|string',
            'totalNum' => 'nullable|integer|min:1',
        ]);
        $payload = [
            'offerId' => $data['offerId'],
            'toProvinceCode' => $data['toProvinceCode'] ?? '330000',
            'toCityCode' => $data['toCityCode'] ?? '330100',
            'toCountryCode' => $data['toCountryCode'] ?? '330108',
            'totalNum' => $data['totalNum'] ?? 1,
        ];
        $freight = $api->alibabaFreightEstimate($payload);
        if (isset($freight['_error'])) {
            return back()->withErrors(['api' => 'Freight xatosi: '.$freight['status']])->withInput();
        }
        session(['mini_freight' => $freight]);
        return redirect()->route('mini.product');
    }

    public function updateLanguage(Request $request)
    {
        $data = $request->validate(['language' => 'required|in:uz,ru,en']);
        $user = auth()->user();
        if ($user) {
            $user->update(['language' => $data['language']]);
        }
        return back()->with('status', 'Til saqlandi');
    }

    public function updateCredentials(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|min:3|max:50|unique:users,username,' . (auth()->id() ?? 'null'),
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user = auth()->user();
        if ($user) {
            $user->username = $data['username'];
            if (!empty($data['password'])) {
                $user->password = bcrypt($data['password']);
            }
            $user->save();
        }
        return back()->with('status', 'Ma\'lumotlar yangilandi');
    }
}


