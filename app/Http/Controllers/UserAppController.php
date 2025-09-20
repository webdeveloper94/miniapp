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
        // Fake telegram user yaratish (local development uchun)
        $this->ensureFakeTelegramUser();
        return view('mini.home');
    }

    private function ensureFakeTelegramUser()
    {
        if (!session('telegram_user')) {
            $fakeUser = [
                'id' => 123456789,
                'username' => 'testuser',
                'first_name' => 'Test',
                'last_name' => 'User',
                'language_code' => 'uz',
                'is_bot' => false,
                'is_premium' => false,
            ];
            session(['telegram_user' => $fakeUser]);
        }
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
        $this->ensureFakeTelegramUser();
        $telegramUser = session('telegram_user');
        $userId = $telegramUser['id'] ?? 1;
        
        $orders = Order::where('user_id', $userId)
            ->with('orderItems')
            ->latest('id')
            ->paginate(10);
            
        return view('mini.orders', compact('orders'));
    }

    public function cart()
    {
        $this->ensureFakeTelegramUser();
        $items = session('cart_items', []);
        return view('mini.cart', compact('items'));
    }

    public function addToCart(Request $request)
    {
        $this->ensureFakeTelegramUser();
        
        $data = $request->validate([
            'product_id' => 'required|string',
            'title' => 'required|string',
            'price' => 'required|numeric',
            'image_url' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'selected_variants' => 'nullable|string', // JSON string for selected size/color
        ]);

        $cartItems = session('cart_items', []);
        $productKey = $data['product_id'] . '_' . ($data['selected_variants'] ?? 'default');
        
        if (isset($cartItems[$productKey])) {
            // Mahsulot allaqachon savatda, miqdorni oshirish
            $cartItems[$productKey]['quantity'] += $data['quantity'];
        } else {
            // Yangi mahsulot qo'shish
            $cartItems[$productKey] = [
                'product_id' => $data['product_id'],
                'title' => $data['title'],
                'price' => $data['price'],
                'image_url' => $data['image_url'],
                'quantity' => $data['quantity'],
                'selected_variants' => $data['selected_variants'],
                'added_at' => now()->toDateTimeString(),
            ];
        }

        session(['cart_items' => $cartItems]);
        
        return back()->with('status', 'Mahsulot savatga qo\'shildi!');
    }

    public function removeFromCart(Request $request)
    {
        $data = $request->validate(['product_key' => 'required|string']);
        
        $cartItems = session('cart_items', []);
        unset($cartItems[$data['product_key']]);
        session(['cart_items' => $cartItems]);
        
        return back()->with('status', 'Mahsulot savatdan olib tashlandi');
    }

    public function updateCartQuantity(Request $request)
    {
        $data = $request->validate([
            'product_key' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);
        
        $cartItems = session('cart_items', []);
        if (isset($cartItems[$data['product_key']])) {
            $cartItems[$data['product_key']]['quantity'] = $data['quantity'];
            session(['cart_items' => $cartItems]);
        }
        
        return back()->with('status', 'Miqdor yangilandi');
    }

    public function viewProductFromCart(Request $request)
    {
        $data = $request->validate([
            'product_key' => 'required|string',
        ]);
        
        $cartItems = session('cart_items', []);
        $cartItem = $cartItems[$data['product_key']] ?? null;
        
        if (!$cartItem) {
            return back()->withErrors(['error' => 'Mahsulot topilmadi']);
        }
        
        // Tanlangan variantlarni olish
        $selectedVariants = json_decode($cartItem['selected_variants'] ?? '{}', true);
        
        // Variantlar uchun skuProps yaratish
        $skuProps = [];
        foreach ($selectedVariants as $variant => $value) {
            $skuProps[] = [
                'name' => $variant,
                'values' => [$value]
            ];
        }
        
        // Mahsulot ma'lumotlarini session da saqlash
        $productData = [
            'data' => [
                'subject' => $cartItem['title'],
                'title' => $cartItem['title'],
                'price' => $cartItem['price'],
                'minPrice' => $cartItem['price'],
                'productImage' => [
                    'images' => $cartItem['image_url'] ? [$cartItem['image_url']] : []
                ],
                'shopName' => 'Do\'kon',
                'productAttribute' => [],
                'productSkuInfos' => [],
                'skuProps' => $skuProps
            ]
        ];
        
        session([
            'mini_product' => $productData,
            'mini_product_link' => 'savatdan',
            'mini_offerId' => $cartItem['product_id']
        ]);
        
        return redirect()->route('mini.product');
    }

    public function profile()
    {
        $this->ensureFakeTelegramUser();
        $payments = Payment::query()->latest('id')->limit(5)->get();
        return view('mini.profile', compact('payments'));
    }

    public function checkout()
    {
        $this->ensureFakeTelegramUser();
        $product = session('mini_product');
        $link = session('mini_product_link');
        $offerId = session('mini_offerId');
        
        if (!$product) {
            return redirect()->route('mini.home')->withErrors(['error' => 'Mahsulot topilmadi']);
        }
        
        return view('mini.checkout', compact('product', 'link', 'offerId'));
    }

    public function createOrder(Request $request)
    {
        $this->ensureFakeTelegramUser();
        
        $data = $request->validate([
            'product_id' => 'required|string',
            'title' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer|min:1',
            'selected_variants' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        // Telegram user ID ni olish
        $telegramUser = session('telegram_user');
        $userId = $telegramUser['id'] ?? 1; // Fallback user ID

        // Buyurtma yaratish
        $order = Order::create([
            'user_id' => $userId,
            'product_url' => session('mini_product_link', ''),
            'source_platform' => '1688',
            'status' => 'pending',
            'total_price' => $data['price'] * $data['quantity'],
            'tracking_number' => null,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
        ]);

        // Order item yaratish
        OrderItem::create([
            'order_id' => $order->id,
            'title' => $data['title'],
            'quantity' => $data['quantity'],
            'unit_price' => $data['price'],
            'subtotal' => $data['price'] * $data['quantity'],
            'image_url' => session('mini_product.data.productImage.images.0', ''),
            'product_params' => $data['selected_variants'],
        ]);

        // Payment yaratish
        Payment::create([
            'user_id' => $userId,
            'order_id' => $order->id,
            'amount' => $data['price'] * $data['quantity'],
            'currency' => 'UZS',
            'status' => 'pending',
            'payment_method' => 'card',
            'transaction_id' => 'TRX' . \Illuminate\Support\Str::random(10),
            'receipt_url' => null,
        ]);

        // Session tozalash
        session()->forget(['mini_product', 'mini_product_link', 'mini_offerId']);

        return redirect()->route('mini.orders')->with('status', 'Buyurtma muvaffaqiyatli yaratildi!');
    }

    public function history()
    {
        $this->ensureFakeTelegramUser();
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
        $this->ensureFakeTelegramUser();
        
        // Telegram user session da tilni yangilash
        $telegramUser = session('telegram_user');
        $telegramUser['language_code'] = $data['language'];
        session(['telegram_user' => $telegramUser]);
        
        return back()->with('status', 'Til saqlandi');
    }

    public function updateCredentials(Request $request)
    {
        $this->ensureFakeTelegramUser();
        
        // Telegram mini app da credentials o'zgartirish kerak emas
        // Lekin fake user uchun session da saqlash
        $data = $request->validate([
            'username' => 'required|string|min:3|max:50',
        ]);

        $telegramUser = session('telegram_user');
        $telegramUser['username'] = $data['username'];
        session(['telegram_user' => $telegramUser]);
        
        return back()->with('status', 'Username yangilandi');
    }
}


