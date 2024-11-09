<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class TermsAgreementController extends Controller
{
    public function show()
    {
        return view('terms-agreement');
    }

    public function showAdmin()
{
    return view('admin.terms-agreement');
}

// 施設管理者用の利用規約同意
public function storeAdmin(Request $request)
{
    $request->validate([
        'terms_accepted' => 'required|accepted',
        'privacy_accepted' => 'required|accepted',
    ], [
        'terms_accepted.required' => '利用規約への同意が必要です',
        'privacy_accepted.required' => 'プライバシーポリシーへの同意が必要です',
    ]);

    $now = Carbon::now();
    
    session([
        'admin_terms_accepted' => true,
        'admin_privacy_accepted' => true,
        'admin_terms_accepted_at' => $now,
        'admin_privacy_accepted_at' => $now
    ]);
    // セッションから取得した値をbooleanに変換
    $termsAccepted = filter_var(session('terms_accepted'), FILTER_VALIDATE_BOOLEAN);
    $privacyAccepted = filter_var(session('privacy_accepted'), FILTER_VALIDATE_BOOLEAN);

    // セッションデータを更新
    session([
        'terms_accepted' => $termsAccepted,
        'privacy_accepted' => $privacyAccepted
    ]);

    return redirect()->route('register');
}

    public function store(Request $request)
{
    $request->validate([
        'terms_accepted' => 'required|accepted',
        'privacy_accepted' => 'required|accepted',
    ], [
        'terms_accepted.required' => '利用規約への同意が必要です',
        'privacy_accepted.required' => 'プライバシーポリシーへの同意が必要です',
    ]);

    $now = Carbon::now();
    
    // セッションに同意情報を保存
    session([
        'terms_accepted' => true,
        'privacy_accepted' => true,
        'terms_accepted_at' => $now,
        'privacy_accepted_at' => $now
    ]);

    // セッションから取得した値をbooleanに変換
    $termsAccepted = filter_var(session('terms_accepted'), FILTER_VALIDATE_BOOLEAN);
    $privacyAccepted = filter_var(session('privacy_accepted'), FILTER_VALIDATE_BOOLEAN);

    // セッションデータを更新
    session([
        'terms_accepted' => $termsAccepted,
        'privacy_accepted' => $privacyAccepted
    ]);

    return redirect()->route('hogosharegister');
}
}