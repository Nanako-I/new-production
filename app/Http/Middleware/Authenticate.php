<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Response;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
{
    if (!$request->expectsJson()) {
        if (($request->is('invitation/*') || 
            $request->is('invitation_staff/*') ||
            $request->is('preregistrationmail/*') ||
            $request->is('hogosharegister/*')) && 
            !$request->hasValidSignature()) {
            abort(403, '期限切れです|施設管理者に招待URLの再送を依頼してください。');
        }
        if ($request->is('invitation/*') || $request->is('invitation_staff/*')) {
            return null; // 署名付きURLの場合はリダイレクトしない
        }
        return route('before-login');
    }
    return null;
}

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // if (! $request->user()) {
        //     return redirect()->route('login'); // ログイン画面にリダイレクト
        // }
        if (! $request->route()->named('hogosharegister') || $request->user()) {
            return parent::handle($request, $next, $guards);
        }


        return $next($request);
    }
}