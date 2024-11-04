<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
{
    $guards = empty($guards) ? [null] : $guards;

    foreach ($guards as $guard) {
        if (Auth::guard($guard)->check()) {
            if ($request->is('invitation/*') || $request->is('invitation_staff/*')) {
                abort(403, 'このURLは有効期限切れです。施設管理者に招待URLの再送を依頼してください。');
            }
            return redirect(RouteServiceProvider::HOME);
        }
    }

    // 署名の検証
    if ($request->is('invitation/*') || $request->is('invitation_staff/*')) {
        if (!$request->hasValidSignature()) {
            abort(403, 'このURLは有効期限切れです。施設管理者に招待URLの再送を依頼してください。');
        }
    }

    return $next($request);
}
}