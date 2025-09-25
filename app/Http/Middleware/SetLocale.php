<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->query('lang');
        if ($lang && in_array($lang, ['uz','ru','en'])) {
            $tg = session('telegram_user', []);
            $tg['language_code'] = $lang;
            session(['telegram_user' => $tg]);
        }

        $current = session('telegram_user.language_code');
        if ($current && in_array($current, ['uz','ru','en'])) {
            App::setLocale($current);
        } else {
            App::setLocale('uz');
        }

        return $next($request);
    }
}


