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

    public function taobaoProductDetail(string $url): array
    {
        $endpoint = '/taobao/product/detail'; // supports short link per docs
        $res = Http::timeout(config('dajisaas.timeout'))
            ->connectTimeout(config('dajisaas.connect_timeout'))
            ->withOptions(['verify' => (bool) config('dajisaas.verify_ssl')])
            ->asJson()->post($this->baseUrl . $endpoint, [
            'appKey' => $this->appKey,
            'appSecret' => $this->appSecret,
            'url' => $url,
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

    public function alibabaProductDetailByOfferId(string $offerId): array
    {
        $endpoint = '/alibaba/product/detail';
        $res = Http::timeout(config('dajisaas.timeout'))
            ->connectTimeout(config('dajisaas.connect_timeout'))
            ->withOptions(['verify' => (bool) config('dajisaas.verify_ssl')])
            ->asJson()->post($this->baseUrl . $endpoint, [
            'appKey' => $this->appKey,
            'appSecret' => $this->appSecret,
            'offerId' => $offerId,
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

    public function alibabaProductDetailByShortUrl(string $url): array
    {
        $endpoint = '/alibaba/product/detailByShortUrl';
        $res = Http::timeout(config('dajisaas.timeout'))
            ->connectTimeout(config('dajisaas.connect_timeout'))
            ->withOptions(['verify' => (bool) config('dajisaas.verify_ssl')])
            ->asJson()->post($this->baseUrl . $endpoint, [
            'appKey' => $this->appKey,
            'appSecret' => $this->appSecret,
            'url' => $url,
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

    public function alibabaQueryProductDetail(string $offerId): array
    {
        $endpoint = '/alibaba/product/queryProductDetail';
        $res = Http::timeout(config('dajisaas.timeout'))
            ->connectTimeout(config('dajisaas.connect_timeout'))
            ->withOptions(['verify' => (bool) config('dajisaas.verify_ssl')])
            ->get($this->baseUrl . $endpoint, [
                'appKey' => $this->appKey,
                'appSecret' => $this->appSecret,
                'offerId' => $offerId,
            ]);
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
        ], $payload);
        $res = Http::asJson()->post($this->baseUrl . $endpoint, $body);
        $res->throw();
        return $res->json('data') ?? [];
    }
}


