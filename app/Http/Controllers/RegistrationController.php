<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Passcode;
use App\Mail\PasscodeMail;


class RegistrationController extends Controller
{
    public function sendPasscode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');
        $passcode = Str::random(6); // ランダムな6桁のパスコードを生成
        $expiresAt = Carbon::now()->addMinutes(120); // 10分後に期限切れ

        // パスコードをデータベースに保存
        Passcode::create([
            'email' => $email,
            'passcode' => $passcode,
            'expires_at' => $expiresAt,
        ]);
        
        // セッションにメールアドレスを保存
    $request->session()->put('email', $email);
        // session(['email' => $email]);
        // $emailsession = $request->session()->get('email');

        // パスコードをメールで送信
        Mail::to($email)->send(new PasscodeMail($passcode));

        // return response()->json(['message' => 'パスコードが送信されました。']);
        // return redirect()->route('passcodeform');
        // JSONレスポンスでリダイレクトURLを返す
        return response()->json(['redirect' => route('passcodeform')]);
    }
    
    public function showPasscodeForm()
    {
        // return view('passcode-form');
    }
    
    public function validatePasscode(Request $request)
    {
        $request->validate([
            'passcode' => 'required|string|size:6',
        ]);
    
        $passcode = $request->input('passcode');
        $email = $request->session()->get('email'); // セッションからメールアドレスを取得
        
        $passcodeRecord = Passcode::where('email', $email)
            ->where('passcode', $passcode)
            ->first();
    
        if (!$passcodeRecord) {
            return back()->withErrors(['passcode' => 'パスコードが一致しません。'])->withInput();
        }
    
        if ($passcodeRecord->expires_at < Carbon::now()) {
            return back()->withErrors(['passcode' => 'パスコードが期限切れです。新しいパスコードを取得してください。'])->withInput();
        }
    
        // パスコードの検証が成功した場合、セッションにフラグを設定
        $request->session()->put('passcode_verified', true);
        return redirect()->route('terms.show');
    }
    
    
    public function showHogoshaRegisterForm()	
    {	
    return view('hogosharegister'); // 適切なビューを返す	
    }
}