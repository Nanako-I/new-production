<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AppendCustomIdForRoles
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $customId = $user->custom_id;
            // 現在のパスを出力して処理を停止
            // dd($request->path());
            Log::info('User is authenticated', ['user_id' => $user->id, 'custom_id' => $customId]);
        
            if ($user->hasAnyRole(['super administrator', 'facility staff administrator', 'facility staff user', 'facility staff reader'])) {
                Log::info('User has the required role', ['roles' => $user->getRoleNames()]);
        
                if (!$request->is("*/$customId*")) {
                    Log::info('Redirecting to URL with custom_id', ['url' => $request->path() . "/$customId"]);
                    return redirect()->to($request->path() . "/$customId");
                }
            }
        }

        return $next($request);
    }
}