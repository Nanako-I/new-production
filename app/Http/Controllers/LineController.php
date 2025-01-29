<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Line;
use LINE\Laravel\Facade\LineLogin;

class LineController extends Controller
{
    private $channelId;
    private $channelSecret;
    private $redirectUri;

    // public function __construct()
    // {
    //     $this->channelId = env('LINE_CHANNEL_ID');
    //     $this->channelSecret = env('LINE_CHANNEL_SECRET');
    //     $this->redirectUri = env('LINE_CALLBACK_URL');
    // }
    public function __construct()
{
    // env()の代わりにconfig()を使用
    $this->channelId = config('services.line.client_id');
    $this->channelSecret = config('services.line.client_secret');
    $this->redirectUri = config('services.line.redirect');
}

    // public function redirectToLine()
    // {
    //     $url = 'https://access.line.me/oauth2/v2.1/authorize?' . http_build_query([
    //         'response_type' => 'code',
    //         'client_id' => $this->channelId,
    //         'redirect_uri' => $this->redirectUri,
    //         'state' => 'random_string', // CSRF対策のためのランダムな文字列
    //         'scope' => 'profile openid', // 必要なスコープ
    //     ]);

    //     return redirect($url);
    // }
    public function redirectToLine()
    {
        // デバッグログの追加
        \Log::info('LINE Login Parameters', [
            'channel_id' => $this->channelId,
            'redirect_uri' => $this->redirectUri
        ]);
    
        $url = 'https://access.line.me/oauth2/v2.1/authorize?' . http_build_query([
            'response_type' => 'code',
            'client_id' => $this->channelId,
            'redirect_uri' => $this->redirectUri,
            'state' => \Str::random(40), // よりセキュアなランダム文字列
            'scope' => 'profile openid',
        ]);
    
        // 生成されたURLをログに記録
        \Log::info('Generated LINE Login URL', ['url' => $url]);
    
        return redirect($url);
    }
    
    public function handleLineCallback(Request $request)
    {
        // LINEからのコールバックを処理
        $code = $request->input('code');

        // アクセストークンを取得
        $response = Http::asForm()->post('https://api.line.me/oauth2/v2.1/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
            'client_id' => $this->channelId,
            'client_secret' => $this->channelSecret,
        ]);

        $data = $response->json();

        if (isset($data['access_token'])) {
            // アクセストークンを使用してユーザー情報を取得
            $userInfoResponse = Http::withToken($data['access_token'])->get('https://api.line.me/v2/profile');
            $userInfo = $userInfoResponse->json();

            // 現在のユーザーを取得
            $user = auth()->user();

             // ユーザー情報を更新
             $user->update([
                'line_name' => $userInfo['displayName'],
                'line_user_id' => $userInfo['userId'],
                'line_profile_picture' => $userInfo['pictureUrl'],
            ]);

            // 保存
            $user->save();

            // QRコードのURLを生成
            $lineId = '@988wfeox'; // あなたのLINEボットのベーシックIDに置き換え
            $qrCodeUrl = "https://line.me/R/ti/p/{$lineId}";

        
           // QRコード表示用のビューに遷移
           return view('lines.qr_code', ['qrCodeUrl' => $qrCodeUrl]);
        }

        return redirect()->route('people.index')->withErrors(['msg' => 'LINEアカウントの連携に失敗しました。']);
    }

    // public function redirectToProvider()
    // {
    //     return LineLogin::redirect();
    // }

    // public function handleProviderCallback(Request $request)
    // {
    //     try {
    //         $token = LineLogin::callback();
    //         $user = LineLogin::userProfile($token);

    //         // ユーザー情報を取得
    //         $lineId = $user->userId;
    //         $displayName = $user->displayName;
    //         $pictureUrl = $user->pictureUrl;

    //         // ここでユーザーのLINE情報をデータベースに保存
    //         $currentUser = auth()->user();
    //         $currentUser->line_user_id = $lineId;
    //         $currentUser->save();

    //         return redirect()->route('profile.edit')->with('status', 'LINEアカウントが連携されました。');
    //     } catch (\Exception $e) {
    //         return redirect()->route('profile.edit')->with('error', 'LINEアカウントの連携に失敗しました。');
    //     }
    // }
    public function generateLoginUrl(Request $request)
    {
        $state = Str::random(40);
        $personId = $request->input('person_id');

        session(['line_state' => $state, 'line_person_id' => $personId]);

        $params = [
            'response_type' => 'code',
            'client_id' => config('services.line.client_id'),
            // 'redirect_uri' => route('line.callback'),
            'redirect_uri' => config('services.line.redirect'),
            'state' => $state,
            'scope' => 'profile openid',
        ];

        $url = 'https://access.line.me/oauth2/v2.1/authorize?' . http_build_query($params);

        return response()->json(['url' => $url]);
    }

    // public function callback(Request $request)
    // {
    //     if ($request->state !== session('line_state')) {
    //         return redirect()->route('home')->with('error', 'Invalid state parameter');
    //     }

    //     // ここでLINEからのアクセストークンを取得し、
    //     // LINEユーザープロフィールを取得し、
    //     // LINEユーザーIDをlinesテーブルに保存します

    //     return redirect()->route('people.edit', session('line_person_id'))->with('success', 'LINEアカウントが正常に連携されました');
    // }

    public function callback(Request $request)
    {
        try {
            $code = $request->code;
            $state = $request->state;

            Log::info('LINE Callback received', [
                'code' => $code,
                'state' => $state,
                'session_state' => session('line_state')
            ]);

            // stateの検証
            if ($state !== session('line_state')) {
                Log::error('State mismatch', [
                    'received_state' => $state,
                    'session_state' => session('line_state')
                ]);
                return redirect()->route('invitation')->with('error', 'セッションが無効です。もう一度お試しください。');
            }

            // アクセストークンの取得
            $response = Http::asForm()->post('https://api.line.me/oauth2/v2.1/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => config('services.line.redirect'),
                'client_id' => config('services.line.client_id'),
                'client_secret' => config('services.line.client_secret'),
            ]);

            if (!$response->successful()) {
                Log::error('Token request failed', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                return redirect()->route('invitation')->with('error', 'LINEとの連携に失敗しました。');
            }

            $tokenData = $response->json();
            
            if (!isset($tokenData['access_token'])) {
                Log::error('Access token not found in response', ['response' => $tokenData]);
                return redirect()->route('invitation')->with('error', 'アクセストークンの取得に失敗しました。');
            }

            // ユーザープロフィールの取得
            $profileResponse = Http::withToken($tokenData['access_token'])
                ->get('https://api.line.me/v2/profile');

            if (!$profileResponse->successful()) {
                Log::error('Profile request failed', [
                    'status' => $profileResponse->status(),
                    'response' => $profileResponse->json()
                ]);
                return redirect()->route('invitation')->with('error', 'プロフィール情報の取得に失敗しました。');
            }

            $profile = $profileResponse->json();

            // ユーザー情報の保存または更新
            $user = User::updateOrCreate(
                ['line_id' => $profile['userId']],
                [
                    'name' => $profile['displayName'],
                    'avatar' => $profile['pictureUrl'] ?? null,
                ]
            );

            // セッションのクリーンアップ
            session()->forget('line_state');

            // ユーザーをログイン状態にする
            auth()->login($user);

            return redirect()->route('line.friends')->with('success', 'LINEアカウントとの連携が完了しました。');

        } catch (\Exception $e) {
            Log::error('LINE Login Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('invitation')->with('error', 'エラーが発生しました。もう一度お試しください。');
        }
    }



    public function showFriends()
    {
        // 友達リストの取得（この部分はLINE Messaging APIの設定が必要）
        $accessToken = 'YOUR_CHANNEL_ACCESS_TOKEN'; // チャネルアクセストークンを設定
        $response = Http::withToken($accessToken)->get('https://api.line.me/v2/bot/followers/ids');
        $friendIds = $response->json()['userIds'];

        return view('line.friends', ['friendIds' => $friendIds]);
    }

    public function storeFriends(Request $request)
    {
        $selectedFriends = $request->input('friends', []);
        $userId = auth()->id();

        foreach ($selectedFriends as $friendId) {
            Line::updateOrCreate(
                ['user_id' => $userId, 'line_id' => $friendId],
                ['status' => 'active']
            );
        }

        return redirect()->route('line.callback')->with('success', '友達が正常に登録されました。');
    }
}
