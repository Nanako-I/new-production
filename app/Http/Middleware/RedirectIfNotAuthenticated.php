<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
{
    // ログインしていない場合
    if (!Auth::check()) {
        $allowedRoutes = [
            'login', 'register', 'forgot-password', 'before-login', 
            'passcodeform', 'send-passcode', 'hogoshalogin', 
            'hogosharegister', 'hogoshanumber', 'staffregister', 'terms-agreement',
            'reset-password/*', 'invitation/*', 'invitation_staff/*', 'preregistrationmail/*', 'admin/terms-agreement'
        ];

        // リクエストが許可されたルートでない場合はリダイレクト
        if (!$request->is($allowedRoutes)) {
            // 署名付きURLの特別な処理
            if (($request->is('invitation/*') || $request->is('invitation_staff/*') || $request->is('preregistrationmail/*'))) {
                if (!$request->hasValidSignature()) {
                    abort(403, 'このURLは期限切れです。施設管理者に招待URLの再送を依頼してください。');
                }
                return $next($request); // 有効な署名を持つリクエストは通す
            }
            return redirect()->route('before-login');
        }
    }

    $response = $next($request);
    
    // レスポンスが正しい型であることを確認
    if (!$response instanceof \Symfony\Component\HttpFoundation\Response) {
        $response = response($response);
    }
    
    return $response;
}
}