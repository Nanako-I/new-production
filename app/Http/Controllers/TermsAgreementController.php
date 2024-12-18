<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TermsAgreementController extends Controller
{
    // 保護者向けの利用規約を表示させる処理はweb.phpに記載




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
    
    // セッションに同意情報を保存（admin_prefixを削除）
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
        'privacy_accepted' => $privacyAccepted,
        'terms_accepted_at' => $now,
        'privacy_accepted_at' => $now
    ]);

    return redirect()->route('register')->with([
        'terms_accepted' => true,
        'privacy_accepted' => true,
        'terms_accepted_at' => $now,
        'privacy_accepted_at' => $now
    ]);
}

public function store(Request $request)
    {
        $request->validate([
            'terms_accepted' => 'required|accepted',
            'privacy_accepted' => 'required|accepted',
            'people_id' => 'required|exists:people,id',
        ], [
            'terms_accepted.required' => '利用規約への同意が必要です',
            'privacy_accepted.required' => 'プライバシーポリシーへの同意が必要です',
            'people_id.required' => 'People IDが必要です',
            'people_id.exists' => '無効なPeople IDです',
        ]);

        $now = Carbon::now();
        
        // セッションに同意情報を保存
        session([
            'terms_accepted' => true,
            'privacy_accepted' => true,
            'terms_accepted_at' => $now,
            'privacy_accepted_at' => $now
        ]);

        // フォームから送信されたpeople_idを取得
        $personId = $request->input('people_id');

        if (!$personId) {
            return redirect()->back()->withErrors(['error' => 'People IDが見つかりません。']);
        }
        // 必要に応じてpersonIdを再度セッションに保存
        session(['temp_person_id' => $personId]);

        // セッションから取得した値をbooleanに変換
        $termsAccepted = filter_var(session('terms_accepted'), FILTER_VALIDATE_BOOLEAN);
        $privacyAccepted = filter_var(session('privacy_accepted'), FILTER_VALIDATE_BOOLEAN);

        // セッションデータを更新
        session([
            'terms_accepted' => $termsAccepted,
            'privacy_accepted' => $privacyAccepted
        ]);

        return redirect()->route('hogosharegister', ['people_id' => $personId]);
    }
}