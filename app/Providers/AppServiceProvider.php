<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set locale from mini app session if available
        try {
            $lang = session('telegram_user.language_code');
            if ($lang && in_array($lang, ['uz','ru','en'])) {
                App::setLocale($lang);
            } else {
                App::setLocale('uz');
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }
}
