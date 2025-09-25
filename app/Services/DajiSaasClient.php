<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DajiSaasClient
{
    private string $baseUrl;
    private string $appKey;
    private string $appSecret;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('dajisaas.base_url'), '/');
        $this->appKey = (string) config('dajisaas.app_key');
        $this->appSecret = (string) config('dajisaas.app_secret');
    }

    /**
     * Preferred language options for API (best-effort).
     * Some endpoints may ignore unknown keys, so we include several commonly used ones.
     */
    private function languageOptions(): array
    {
        // Try to respect mini app user's language; fallback to app locale; default 'en'
        $lang = session('telegram_user.language_code') ?? app()->getLocale() ?? 'en';
        $lang = in_array($lang, ['uz','ru','en']) ? $lang : 'en';
        $locale = $lang === 'ru' ? 'ru_RU' : ($lang === 'uz' ? 'uz_UZ' : 'en_US');
        return [
            'language' => $lang,
            'lang' => $lang,
            'locale' => $locale,
            'translate' => true,
            'needTranslate' => true,
        ];
    }

    public function taobaoProductDetail(string $url): array
    {
        // Try different possible endpoints for Taobao
        $endpoints = [
            '/taobao/product/detail',
            '/taobao/product/info',
            '/taobao/product/getDetail',
            '/taobao/item/detail',
            '/taobao/item/info'
        ];
        
        foreach ($endpoints as $endpoint) {
            $res = Http::retry(2, 500)->timeout(config('dajisaas.timeout'))
                ->connectTimeout(config('dajisaas.connect_timeout'))
                ->withOptions(['verify' => (bool) config('dajisaas.verify_ssl')])
                ->asJson()->post($this->baseUrl . $endpoint, array_merge([
                'appKey' => $this->appKey,
                'appSecret' => $this->appSecret,
                'url' => $url,
            ], $this->languageOptions()));
            
            if ($res->successful()) {
                $json = $res->json();
                return $json['data'] ?? $json ?? [];
            }
            
            // If not 404, return the error
            if ($res->status() !== 404) {
                return [
                    '_error' => true,
                    'status' => $res->status(),
                    'body' => $res->body(),
                    'endpoint' => $endpoint,
                ];
            }
        }
        
        // All endpoints returned 404
        return [
            '_error' => true,
            'status' => 404,
            'body' => 'All Taobao endpoints returned 404',
            'endpoint' => 'multiple',
        ];
    }

    public function taobaoProductDetailByItemId(string $itemId): array
    {
        $endpoint = '/taobao/traffic/item/get';
        $res = Http::retry(2, 500)->timeout(config('dajisaas.timeout'))
            ->connectTimeout(config('dajisaas.connect_timeout'))
            ->withOptions(['verify' => (bool) config('dajisaas.verify_ssl')])
            ->get($this->baseUrl . $endpoint, array_merge([
                'appKey' => $this->appKey,
                'appSecret' => $this->appSecret,
                'item_id' => $itemId,
            ], $this->languageOptions()));
        
        // Log for debugging
        \Log::info('Taobao API request', [
            'endpoint' => $endpoint,
            'item_id' => $itemId,
            'status' => $res->status(),
            'body' => $res->body()
        ]);
        
        if ($res->successful()) {
            $json = $res->json();
            return $json['data'] ?? $json ?? [];
        }
        
        return [
            '_error' => true,
            'status' => $res->status(),
            'body' => $res->body(),
            'endpoint' => $endpoint,
        ];
    }

    public function taobaoUploadImage(string $imageBase64): array
    {
        $endpoint = '/taobao/upload/image';
        $res = Http::retry(2, 500)->timeout(config('dajisaas.timeout'))
            ->connectTimeout(config('dajisaas.connect_timeout'))
            ->withOptions(['verify' => (bool) config('dajisaas.verify_ssl')])
            ->asMultipart()->post($this->baseUrl . $endpoint, array_merge([
            'appKey' => $this->appKey,
            'appSecret' => $this->appSecret,
            'image_base64' => $imageBase64,
        ], $this->languageOptions()));
        
        if ($res->successful()) {
            $json = $res->json();
            return $json['data'] ?? $json ?? [];
        }
        
        return [
            '_error' => true,
            'status' => $res->status(),
            'body' => $res->body(),
            'endpoint' => $endpoint,
        ];
    }

    public function taobaoProductDetailByShortUrl(string $url): array
    {
        $endpoint = '/taobao/product/detailByShortUrl';
        $res = Http::retry(2, 500)->timeout(config('dajisaas.timeout'))
            ->connectTimeout(config('dajisaas.connect_timeout'))
            ->withOptions(['verify' => (bool) config('dajisaas.verify_ssl')])
            ->asJson()->post($this->baseUrl . $endpoint, array_merge([
            'appKey' => $this->appKey,
            'appSecret' => $this->appSecret,
            'url' => $url,
        ], $this->languageOptions()));
        if ($res->successful()) {
            $json = $res->json();
            return $json['data'] ?? $json ?? [];
        }
        return [
            '_error' => true,
            'status' => $res->status(),
            'body' => $res->body(),
            'endpoint' => $endpoint,
        ];
    }

    public function taobaoProductInfo(string $itemId): array
    {
        $endpoint = '/taobao/product/info';
        $res = Http::retry(2, 500)->timeout(config('dajisaas.timeout'))
            ->connectTimeout(config('dajisaas.connect_timeout'))
            ->withOptions(['verify' => (bool) config('dajisaas.verify_ssl')])
            ->asJson()->post($this->baseUrl . $endpoint, array_merge([
            'appKey' => $this->appKey,
            'appSecret' => $this->appSecret,
            'itemId' => $itemId,
        ], $this->languageOptions()));
        if ($res->successful()) {
            $json = $res->json();
            return $json['data'] ?? $json ?? [];
        }
        return [
            '_error' => true,
            'status' => $res->status(),
            'body' => $res->body(),
            'endpoint' => $endpoint,
        ];
    }

    public function taobaoProductSearch(string $keyword): array
    {
        $endpoint = '/taobao/product/search';
        $res = Http::retry(2, 500)->timeout(config('dajisaas.timeout'))
            ->connectTimeout(config('dajisaas.connect_timeout'))
            ->withOptions(['verify' => (bool) config('dajisaas.verify_ssl')])
            ->asJson()->post($this->baseUrl . $endpoint, array_merge([
            'appKey' => $this->appKey,
            'appSecret' => $this->appSecret,
            'keyword' => $keyword,
        ], $this->languageOptions()));
        if ($res->successful()) {
            $json = $res->json();
            return $json['data'] ?? $json ?? [];
        }
        return [
            '_error' => true,
            'status' => $res->status(),
            'body' => $res->body(),
            'endpoint' => $endpoint,
        ];
    }

    public function taobaoProductDetailByUrl(string $url): array
    {
        $endpoint = '/taobao/product/detailByUrl';
        $res = Http::retry(2, 500)->timeout(config('dajisaas.timeout'))
            ->connectTimeout(config('dajisaas.connect_timeout'))
            ->withOptions(['verify' => (bool) config('dajisaas.verify_ssl')])
            ->asJson()->post($this->baseUrl . $endpoint, array_merge([
            'appKey' => $this->appKey,
            'appSecret' => $this->appSecret,
            'url' => $url,
        ], $this->languageOptions()));
        if ($res->successful()) {
            $json = $res->json();
            return $json['data'] ?? $json ?? [];
        }
        return [
            '_error' => true,
            'status' => $res->status(),
            'body' => $res->body(),
            'endpoint' => $endpoint,
        ];
    }

    public function alibabaProductDetailByOfferId(string $offerId): array
    {
        $endpoint = '/alibaba/product/detail';
        $res = Http::retry(2, 500)->timeout(config('dajisaas.timeout'))
            ->connectTimeout(config('dajisaas.connect_timeout'))
            ->withOptions(['verify' => (bool) config('dajisaas.verify_ssl')])
            ->asJson()->post($this->baseUrl . $endpoint, array_merge([
            'appKey' => $this->appKey,
            'appSecret' => $this->appSecret,
            'offerId' => $offerId,
        ], $this->languageOptions()));
        if ($res->successful()) {
            $json = $res->json();
            return $json['data'] ?? $json ?? [];
        }
        return [
            '_error' => true,
            'status' => $res->status(),
            'body' => $res->body(),
            'endpoint' => $endpoint,
        ];
    }

    public function alibabaProductDetailByShortUrl(string $url): array
    {
        $endpoint = '/alibaba/product/detailByShortUrl';
        $res = Http::retry(2, 500)->timeout(config('dajisaas.timeout'))
            ->connectTimeout(config('dajisaas.connect_timeout'))
            ->withOptions(['verify' => (bool) config('dajisaas.verify_ssl')])
            ->asJson()->post($this->baseUrl . $endpoint, array_merge([
            'appKey' => $this->appKey,
            'appSecret' => $this->appSecret,
            'url' => $url,
        ], $this->languageOptions()));
        if ($res->successful()) {
            $json = $res->json();
            return $json['data'] ?? $json ?? [];
        }
        return [
            '_error' => true,
            'status' => $res->status(),
            'body' => $res->body(),
            'endpoint' => $endpoint,
        ];
    }

    public function alibabaQueryProductDetail(string $offerId): array
    {
        $endpoint = '/alibaba/product/queryProductDetail';
        $res = Http::timeout(config('dajisaas.timeout'))
            ->connectTimeout(config('dajisaas.connect_timeout'))
            ->withOptions(['verify' => (bool) config('dajisaas.verify_ssl')])
            ->get($this->baseUrl . $endpoint, array_merge([
                'appKey' => $this->appKey,
                'appSecret' => $this->appSecret,
                'offerId' => $offerId,
            ], $this->languageOptions()));
        if ($res->successful()) {
            return $res->json('data') ?? [];
        }
        return [
            '_error' => true,
            'status' => $res->status(),
            'body' => $res->body(),
            'endpoint' => $endpoint,
        ];
    }

    public function alibabaFreightEstimate(array $payload): array
    {
        $endpoint = '/alibaba/product/freightEstimate';
        $body = array_merge([
            'appKey' => $this->appKey,
            'appSecret' => $this->appSecret,
        ], $payload, $this->languageOptions());
        $res = Http::asJson()->post($this->baseUrl . $endpoint, $body);
        $res->throw();
        return $res->json('data') ?? [];
    }
}


