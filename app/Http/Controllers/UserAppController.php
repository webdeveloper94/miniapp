<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
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
            // Mavjud user ni topish (seeder orqali yaratilgan)
            $user = \App\Models\User::where('role', 'user')->first();
            
            if (!$user) {
                // Agar user yo'q bo'lsa, oddiy user yaratish
                $user = \App\Models\User::create([
                    'name' => 'Test User',
                    'email' => 'testuser@example.com',
                    'password' => bcrypt('password'),
                    'username' => 'testuser',
                    'phone' => '+998901234567',
                    'role' => 'user',
                    'language' => 'uz'
                ]);
            }
            
            $fakeUser = [
                'id' => $user->id,
                'username' => $user->username,
                'first_name' => 'Test',
                'last_name' => 'User',
                'language_code' => 'uz',
                'is_bot' => false,
                'is_premium' => false,
            ];
            session(['telegram_user' => $fakeUser]);
        } else {
            // Session mavjud, lekin user_id ni tekshirish
            $telegramUser = session('telegram_user');
            $userId = $telegramUser['id'] ?? null;
            
            if ($userId && !\App\Models\User::where('id', $userId)->exists()) {
                // Agar session da user_id mavjud emas bo'lsa, yangi user yaratish
                $user = \App\Models\User::where('role', 'user')->first();
                if (!$user) {
                    $user = \App\Models\User::create([
                        'name' => 'Test User',
                        'email' => 'testuser@example.com',
                        'password' => bcrypt('password'),
                        'username' => 'testuser',
                        'phone' => '+998901234567',
                        'role' => 'user',
                        'language' => 'uz'
                    ]);
                }
                
                // Session ni yangilash
                $fakeUser = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'first_name' => 'Test',
                    'last_name' => 'User',
                    'language_code' => 'uz',
                    'is_bot' => false,
                    'is_premium' => false,
                ];
                session(['telegram_user' => $fakeUser]);
            }
        }
    }

    public function findProduct(Request $request, DajiSaasClient $api)
    {
        $data = $request->validate(['link' => 'required|string']);
        $link = $data['link'];
        $originalLink = $link;

        // 1) Agar qisqa Taobao link bo'lsa, uni to'liq manzilga yechib olishga urinib ko'ramiz
        if ($this->looksLikeShortUrl($link)) {
            $resolved = $this->resolveFinalUrl($link);
            if (!empty($resolved)) {
                $link = $resolved;
                \Log::info('Short URL resolved', ['original' => $originalLink, 'resolved' => $link]);
            } else {
                \Log::warning('Short URL resolve failed, will try shortUrl API fallback', ['url' => $link]);
            }
        }
        // Detect platform and fetch accordingly
        $offerIdForSession = null;
        $isTaobao = preg_match('~(taobao\.com|tmall\.com|item\.taobao|detail\.tmall)~i', $link);
        $is1688 = preg_match('~(1688\.com|offer/)~i', $link);
        
        if ($is1688) {
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
                // QR 1688 qisqa linklarida shortUrl endpoint ko'p hollarda 404 qaytaradi.
                // Shuning uchun avval yechishga yana urinib, offerId ajratamiz.
                $resolved = $this->resolveFinalUrl($originalLink);
                if ($resolved && preg_match('~/(offer)/(\d+)~', $resolved, $mx)) {
                    $offerId = $mx[2];
                    $offerIdForSession = $offerId;
                    $product = $api->alibabaQueryProductDetail($offerId);
                    if (isset($product['_error'])) {
                        $product = $api->alibabaProductDetailByOfferId($offerId);
                    }
                } else {
                    return back()->withErrors(['api' => '1688 qisqa linkni yechib bo\'lmadi. Iltimos, mahsulotning to\'liq 1688 linkini yuboring.']);
                }
            }
        } elseif ($isTaobao) {
            // Extract itemId from Taobao URL
            preg_match('~[?&]id=(\d+)~', $link, $matches);
            $itemId = $matches[1] ?? null;
            
            if ($itemId) {
                // Try Taobao API with itemId using /taobao/traffic/item/get endpoint
                $product = $api->taobaoProductDetailByItemId($itemId);
                
                if (isset($product['_error'])) {
                    return back()->withErrors(['api' => 'Taobao mahsulot topilmadi. Iltimos, boshqa linkni sinab ko\'ring. API xatosi: ' . $product['status']]);
                }

                // Normalize Taobao payload to our common structure used by the mini product page
                $product = ['data' => $this->normalizeTaobaoProduct(is_array($product) ? $product : (array) $product)];
            } else {
                // 2) Agar item_id topilmasa, qisqa link endpointidan foydalanamiz
                $product = $api->taobaoProductDetailByShortUrl($originalLink);
                if (isset($product['_error'])) {
                    return back()->withErrors(['api' => "Taobao short-url yechilmadi. Xato: ".$product['status']]);
                }
                $product = ['data' => $this->normalizeTaobaoProduct((array) $product)];
            }
        } else {
            return back()->withErrors(['api' => 'Qo\'llab-quvvatlanmaydigan link. Faqat 1688.com yoki Taobao.com linklarini kiriting.']);
        }
        
        // Check for any remaining errors
        if (isset($product['_error'])) {
            return back()->withErrors(['api' => 'API xatosi: '.$product['status'].' '.$product['endpoint']]);
        }
        // Persist product in session for redirect safety
        session(['mini_product' => $product, 'mini_product_link' => $link, 'mini_offerId' => $offerIdForSession]);
        return redirect()->route('mini.product');
    }

    private function looksLikeShortUrl(string $url): bool
    {
        return (bool) preg_match('~(e\.tb\.cn|m\.tb\.cn|s\.click\.taobao\.com|tb\.cn|t\.cn|qr\.1688\.com)~i', $url);
    }

    private function resolveFinalUrl(string $url): ?string
    {
        try {
            $attempts = [
                ['ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148', 'scheme' => null],
                ['ua' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'scheme' => null],
            ];
            $deadline = microtime(true) + 12; // fail-fast: max 12s overall
            foreach ($attempts as $conf) {
                $target = $url;
                if ($conf['scheme']) {
                    $target = preg_replace('~^https://~i', $conf['scheme'] . '://', $target);
                }
                $client = new \GuzzleHttp\Client([
                    'allow_redirects' => true,
                    'timeout' => 7,
                    'connect_timeout' => 4,
                    'read_timeout' => 7,
                    'verify' => false,
                    'http_errors' => false,
                    'cookies' => new \GuzzleHttp\Cookie\CookieJar(),
                    'headers' => [
                        'User-Agent' => $conf['ua'],
                        'Accept-Language' => 'zh-CN,zh;q=0.9,en;q=0.8',
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                        'Referer' => 'https://m.1688.com/'
                    ]
                ]);
                try {
                    $res = $client->get($target);
                } catch (\Throwable $inner) {
                    if (microtime(true) > $deadline) return null;
                    continue; // try next attempt
                }
                $eff = $res->getHeader('X-Guzzle-Effective-Url');
                if (!empty($eff)) return $eff[0];
                $body = (string) $res->getBody();
                // Meta refresh redirect
                if (preg_match('~<meta[^>]+http-equiv=["\']refresh["\'][^>]+content=["\']\d+;\s*url=([^"\']+)["\']~i', $body, $mm)) {
                    return html_entity_decode($mm[1]);
                }
                // JS location redirects
                if (preg_match('~location\.(?:href|replace)\(["\']([^"\']+)["\']\)~i', $body, $jm)) {
                    return $jm[1];
                }
                // Handle 1688 QR short page: wireless1688://...url=<encoded>
                if (preg_match('~wireless1688://[^\s]+url=([^&\s]+)~i', $body, $wm)) {
                    $decoded = urldecode($wm[1]);
                    return $decoded;
                }
                if (preg_match('~https?://(?:item\.taobao\.com|detail\.tmall\.com|detail\.1688\.com|m\.1688\.com/offer/|m\.1688\.com/detail/)[^\s\"\']+~i', $body, $m)) {
                    return $m[0];
                }
                // Last resort: extract offerId pattern from raw
                if (preg_match("~offerId[=:\\\"'](\\d{6,})~i", $body, $oid)) {
                    return 'https://m.1688.com/offer/' . $oid[1] . '.html';
                }
                if (microtime(true) > $deadline) return null;
            }
            return null;
        } catch (\Throwable $e) {
            \Log::warning('Short URL resolve failed', ['url' => $url, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function productPage()
    {
        $product = session('mini_product');
        $link = session('mini_product_link');
        $offerId = session('mini_offerId');
        abort_unless($product, 404);
        return view('mini.product', compact('product', 'link', 'offerId'));
    }

    /**
     * Taobao API javobini mini product sahifasi ishlata oladigan umumiy formatga keltiradi.
     * Kutilgan format: title/subject, price/minPrice, productImage.images[], shopName, skuProps[]
     */
    private function normalizeTaobaoProduct(array $raw): array
    {
        // Many vendors wrap data in 'data' or 'result' keys
        $p = $raw['data'] ?? $raw['result'] ?? $raw;

        // Some responses nest 'item'
        $item = $p['item'] ?? $p['itemInfo'] ?? [];
        $title = $p['title'] ?? $p['subject'] ?? ($item['title'] ?? ($p['item_title'] ?? 'Mahsulot'));
        $shop = $p['shopName'] ?? ($p['seller'] ?? ($p['sellerNick'] ?? ($p['shop'] ?? '-')));

        // Price: try several common places
        $price = $p['price'] ?? $p['minPrice'] ?? $p['discountPrice'] ?? ($item['price'] ?? null);

        // Images: merge from known locations (including sample schema)
        $images = [];
        $candidates = [];
        if (!empty($p['images'])) $candidates[] = $p['images'];
        if (!empty($p['imageList'])) $candidates[] = $p['imageList'];
        if (!empty($p['gallery'])) $candidates[] = $p['gallery'];
        if (!empty($p['picUrls'])) $candidates[] = $p['picUrls'];
        if (!empty($p['mainImageUrl'])) $candidates[] = [$p['mainImageUrl']];
        if (!empty($p['propertyImageList'])) $candidates[] = array_map(function ($i) { return $i['imageUrl'] ?? null; }, (array) $p['propertyImageList']);
        if (!empty($p['skuList'])) $candidates[] = array_map(function ($s) { return $s['picUrl'] ?? null; }, (array) $p['skuList']);
        if (!empty($p['detailImages'])) $candidates[] = $p['detailImages'];
        if (!empty($p['itemImages'])) $candidates[] = array_map(function ($i) { return $i['url'] ?? $i; }, (array) $p['itemImages']);
        if (!empty($item['images'])) $candidates[] = (array) $item['images'];
        if (!empty($p['picUrl'])) $candidates[] = [$p['picUrl']];
        if (!empty($p['image'])) $candidates[] = [$p['image']];
        foreach ($candidates as $arr) {
            foreach ((array) $arr as $u) {
                $u = is_string($u) ? $u : ($u['url'] ?? ($u['fullPathImageURI'] ?? ($u['imageUrl'] ?? '')));
                if (!$u) continue;
                // Fix protocol-relative urls
                if (str_starts_with($u, '//')) $u = 'https:' . $u;
                $images[] = $u;
            }
        }
        // Pull images from rich HTML description if present
        if (!empty($p['description']) && is_string($p['description'])) {
            if (preg_match_all('~src=\"(.*?)\"~', $p['description'], $m)) {
                foreach ($m[1] as $u) {
                    if (str_starts_with($u, '//')) $u = 'https:' . $u;
                    $images[] = $u;
                }
            }
        }
        $images = array_values(array_unique(array_filter($images)));

        // skuProps: Taobao often uses skuBase.props
        $skuProps = [];
        $skuBase = $p['skuBase'] ?? $p['sku'] ?? [];
        $props = $skuBase['props'] ?? $skuBase['skuProps'] ?? $p['skuProps'] ?? [];
        foreach ((array) $props as $prop) {
            $name = $prop['name'] ?? ($prop['prop'] ?? 'Option');
            $values = [];
            $pv = $prop['values'] ?? $prop['value'] ?? $prop['items'] ?? $prop['vids'] ?? [];
            foreach ((array) $pv as $val) {
                if (is_string($val)) { $values[] = $val; continue; }
                $values[] = $val['name'] ?? ($val['value'] ?? ($val['valueName'] ?? ($val['valueDisplayName'] ?? null)));
            }
            $values = array_values(array_filter($values));
            if ($values) {
                $skuProps[] = ['name' => $name, 'values' => $values];
            }
        }

        // If still empty, derive from provided skuList (sample schema)
        if (empty($skuProps) && !empty($p['skuList']) && is_array($p['skuList'])) {
            $map = [];
            foreach ($p['skuList'] as $sku) {
                foreach (($sku['properties'] ?? []) as $pr) {
                    $name = $pr['propName'] ?? ($pr['name'] ?? 'Option');
                    $val = $pr['valueName'] ?? ($pr['value'] ?? null);
                    if (!$val) continue;
                    $map[$name] = $map[$name] ?? [];
                    $map[$name][$val] = true;
                }
            }
            foreach ($map as $name => $vals) {
                $skuProps[] = ['name' => $name, 'values' => array_keys($vals)];
            }
        }

        return [
            'title' => $title,
            'subject' => $title,
            'shopName' => $shop,
            'price' => $price,
            'minPrice' => $price,
            'productImage' => ['images' => $images],
            'skuProps' => $skuProps,
            // keep some raw fields for debugging if needed
            '_source' => 'taobao',
        ];
    }

    public function orders()
    {
        $this->ensureFakeTelegramUser();
        $telegramUser = session('telegram_user');
        $userId = $telegramUser['id'] ?? 1;
        
        $orders = Order::where('user_id', $userId)
            ->with(['orderItems', 'payment'])
            ->latest('id')
            ->paginate(10);

        // Admin ma'lumotlarini olish
        $adminSettings = \App\Models\AdminSetting::pluck('value', 'key');
            
        return view('mini.orders', compact('orders', 'adminSettings'));
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
        try {
            $this->ensureFakeTelegramUser();
            
            $data = $request->validate([
                'product_id' => 'nullable|string',
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

            // If product has variants in session, ensure all selected
            $product = session('mini_product');
            $hasVariants = false;
            $requiredGroups = [];
            if ($product){
                $p = $product['data'] ?? $product;
                $skuProps = $p['skuProps'] ?? [];
                if (empty($skuProps) && !empty($p['skuBase']['props'])){
                    $skuProps = $p['skuBase']['props'];
                }
                if (!empty($skuProps)){
                    $hasVariants = true;
                    foreach ($skuProps as $sp){
                        $requiredGroups[] = $sp['name'] ?? ($sp['prop'] ?? 'Option');
                    }
                }
            }
            if ($hasVariants){
                $selected = json_decode($data['selected_variants'] ?? '{}', true);
                foreach ($requiredGroups as $g){
                    if (empty($selected[$g])){
                        return back()->withErrors(['error' => "Iltimos, variant tanlang: $g"])->withInput();
                    }
                }
            }

            // Telegram user ID ni olish va tekshirish
            $telegramUser = session('telegram_user');
            $userId = $telegramUser['id'] ?? 1; // Fallback user ID
            
            // User mavjudligini tekshirish
            if (!\App\Models\User::where('id', $userId)->exists()) {
                // Agar user mavjud bo'lmasa, yangi user yaratish
                $user = \App\Models\User::where('role', 'user')->first();
                if (!$user) {
                    $user = \App\Models\User::create([
                        'name' => 'Test User',
                        'email' => 'testuser@example.com',
                        'password' => bcrypt('password'),
                        'username' => 'testuser',
                        'phone' => '+998901234567',
                        'role' => 'user',
                        'language' => 'uz'
                    ]);
                }
                $userId = $user->id;
                
                // Session ni yangilash
                $fakeUser = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'first_name' => 'Test',
                    'last_name' => 'User',
                    'language_code' => 'uz',
                    'is_bot' => false,
                    'is_premium' => false,
                ];
                session(['telegram_user' => $fakeUser]);
            }

            // Service fee calculation
            $unitPrice = (float) $data['price'];
            $qty = (int) $data['quantity'];
            $baseTotal = $unitPrice * $qty;
            // Xizmat haqi narx oralig'i bo'yicha: jami bazaviy summaga qarab aniqlanadi
            $serviceFeeRule = \App\Models\ServiceFee::getFeeForAmount($baseTotal);
            $servicePercent = $serviceFeeRule?->fee_percentage ?? 0;
            $serviceAmount = round(($baseTotal * $servicePercent) / 100, 2);

            // Buyurtma yaratish
            $order = Order::create([
                'user_id' => $userId,
                'product_url' => session('mini_product_link', ''),
                'source_platform' => '1688',
                'status' => 'pending',
                'total_price' => $baseTotal + $serviceAmount,
                'service_fee_percent' => $servicePercent,
                'service_fee_amount' => $serviceAmount,
                'tracking_number' => null,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'],
                'address' => $data['address'],
            ]);

            // Rasm URL ni olish
            $product = session('mini_product');
            $imageUrl = '';
            if (isset($product['data']['productImage']['images'][0])) {
                $imageUrl = $product['data']['productImage']['images'][0];
            } elseif (isset($product['productImage']['images'][0])) {
                $imageUrl = $product['productImage']['images'][0];
            }

            // Order item yaratish
            OrderItem::create([
                'order_id' => $order->id,
                'title' => $data['title'],
                'quantity' => $data['quantity'],
                'unit_price' => $data['price'],
                'subtotal' => $data['price'] * $data['quantity'],
                'image_url' => $imageUrl,
                'product_params' => $data['selected_variants'],
            ]);

            // Payment yaratish
            try {
                $payment = Payment::create([
                    'user_id' => $userId,
                    'order_id' => $order->id,
                    'amount' => $baseTotal + $serviceAmount,
                    'currency' => 'UZS',
                    'status' => 'pending',
                    'payment_method' => 'card',
                    'transaction_id' => 'TRX' . \Illuminate\Support\Str::random(10),
                    'receipt_url' => null,
                ]);
                
                \Log::info('Payment created successfully', [
                    'payment_id' => $payment->id,
                    'order_id' => $order->id,
                    'amount' => $data['price'] * $data['quantity']
                ]);
            } catch (\Exception $e) {
                \Log::error('Payment creation failed', [
                    'error' => $e->getMessage(),
                    'order_id' => $order->id
                ]);
                throw $e;
            }

            // Session tozalash
            session()->forget(['mini_product', 'mini_product_link', 'mini_offerId']);

            return redirect()->route('mini.orders')->with('status', 'Buyurtma muvaffaqiyatli yaratildi!');
            
        } catch (\Exception $e) {
            \Log::error('Order creation error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Buyurtma yaratishda xato: ' . $e->getMessage()]);
        }
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

    public function submitPayment(Request $request)
    {
        try {
            $this->ensureFakeTelegramUser();
            
            $data = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'card_number' => 'required|string|max:20',
                'amount' => 'required|numeric|min:1',
                'receipt_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'note' => 'nullable|string|max:500',
            ]);

            $telegramUser = session('telegram_user');
            $userId = $telegramUser['id'] ?? 1;

            // Rasmni saqlash
            $receiptPath = $request->file('receipt_image')->store('receipts', 'public');

            // Yangi Payment yaratish
            $payment = Payment::create([
                'user_id' => $userId,
                'order_id' => $data['order_id'],
                'amount' => $data['amount'],
                'currency' => 'UZS',
                'status' => 'pending',
                'payment_method' => 'card',
                'transaction_id' => 'TRX' . \Illuminate\Support\Str::random(10),
                'receipt_url' => $receiptPath,
                'note' => $data['note'],
                'card_number' => $data['card_number'],
            ]);
            
            \Log::info('Payment created successfully', [
                'payment_id' => $payment->id,
                'order_id' => $data['order_id'],
                'amount' => $data['amount'],
                'receipt_url' => $receiptPath
            ]);

            return redirect()->route('mini.orders')->with('status', 'To\'lov muvaffaqiyatli yuborildi! Admin tasdiqlashini kuting.');
            
        } catch (\Exception $e) {
            \Log::error('Payment submission error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'To\'lov yuborishda xato: ' . $e->getMessage()]);
        }
    }
}


