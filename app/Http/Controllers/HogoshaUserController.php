<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

use App\Models\User;
use App\Models\Facility;
use App\Models\Person;
use App\Models\Role;
use App\Models\Chat;

class HogoshaUserController extends Controller
{
    public function showRegister()
   {
       return view('hogosharegister');
   }
   
   public function register(Request $request)
{
    try {
        $validatedData = $request->validate([
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name_kana' => ['nullable', 'string', 'max:255'],
            'first_name_kana' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/' // 大文字小文字英数字含む
            ]
        ]);

        // バリデーションが成功した場合の処理
        $userData = [
            'last_name' => $validatedData['last_name'],
            'first_name' => $validatedData['first_name'],
            'last_name_kana' => $validatedData['last_name_kana'],
            'first_name_kana' => $validatedData['first_name_kana'],
            'email' => $validatedData['email'],
            // 'password' => Hash::make($validatedData['password']), // セッションに保存する時にもハッシュ化するとPWが二重でハッシュ化されてしまう
            'password' => $validatedData['password'],
            'terms_accepted' => session('terms_accepted', false),
            'privacy_accepted' => session('privacy_accepted', false),
            'terms_accepted_at' => session('terms_accepted_at'),
            'privacy_accepted_at' => session('privacy_accepted_at')
        ];

        $request->session()->put('user_data', $userData);

        return view('hogoshanumber', compact('userData'));

    } catch (ValidationException $e) {
        // バリデーションが失敗した場合の処理
        return view('hogosharegister')
                         ->withErrors($e->errors())
                         ->withInput();
    }
}

  public function edit(Request $request)
    {
        $user = auth()->user();

        // ログインしているユーザーに関連する人物を取得
        $people = $user->people_family()->get();
        // 各Personに対して未読メッセージがあるかを確認
        foreach ($people as $person) {
            $unreadMessages = Chat::where('people_id', $person->id)
                                ->where('is_read', false)
                                ->where('user_identifier', '!=', $user->id)
                                ->exists();
            $person->unreadMessages = $unreadMessages;
        }


        \Log::info('HogoshaUserController edit method started.');
        return view('hogosha', compact('people'));
    }


   public function hogosha()
  {
      // 現在ログインしているユーザーを取得
      $user = Auth::user();

      // ユーザーが関連付けられている全てのPerson（利用者）を取得
      $people = $user->people_family()->get();


      // データをビューに渡す
      return view('hogosha', compact('people'));
  }
   
   public function create()
   {
       return view('hogoshanumber');
   }

   
   public function numberregister(Request $request)
{
   
      // バリデーションルールとメッセージを定義
    $rules = [
        'jukyuusha_number' => 'required|digits:10',
        'date_of_birth' => 'required|date_format:Y-m-d', // 必要に応じてフォーマットを調整
    ];

    $messages = [
        'jukyuusha_number.required' => '受給者証番号は必須です。',
        'jukyuusha_number.digits' => '受給者証番号は10桁で入力してください。',
        'date_of_birth.required' => '生年月日は必須です。',
        'date_of_birth.date_format' => '生年月日は正しい形式で入力してください。',
    ];

    // バリデーションを実行
    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
        // バリデーションに失敗した場合、エラーメッセージとともに同じビューを返す
        return view('hogoshanumber', [
            'errors' => $validator->errors(),
            'input' => $request->all()
        ]);
    }
    // 受給者証番号と生年月日で人を検索
    $person = Person::where('jukyuusha_number', $request->jukyuusha_number)
                    ->where('date_of_birth', $request->date_of_birth)
                    ->first();
    // dd($person);
        // 人が見つかった場合
        if ($person) {
            // セッションから登録データを取得
            $registerData = $request->session()->get('user_data');
            //  dd($registerData);
            
            if (!$registerData) {
            $error = 'セッションの登録データが見つかりませんでした。';
            return view('hogoshanumber', compact('error'));
        }

        try {
            DB::beginTransaction();

            $user = User::query()->create([
                'last_name' => $registerData['last_name'],
                'first_name' => $registerData['first_name'],
                'last_name_kana' => $registerData['last_name_kana'],
                'first_name_kana' => $registerData['first_name_kana'],
                'email' => $registerData['email'],
                'password' => Hash::make($registerData['password']),
                'terms_accepted' => $registerData['terms_accepted'],
                'privacy_accepted' => $registerData['privacy_accepted'],
                'terms_accepted_at' => $registerData['terms_accepted_at'],
                'privacy_accepted_at' => $registerData['privacy_accepted_at'],
            ]);

            Auth::login($user);

            $user->people_family()->syncWithoutDetaching($person->id);
            $people = $user->people_family()->get();
            $user->assignRole('client family user');

            DB::commit();
            // 未読メッセージを取得
            $unreadMessages = Chat::where('people_id', $person->id)
            ->where('is_read', false)
            ->where('user_identifier', '!=', $user->id)
            ->exists();
            // hogosha ビューにデータを渡して表示
            return view('hogosha', compact('people', 'unreadMessages'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('登録処理中のエラー: ' . $e->getMessage());
            $error = '登録処理中にエラーが発生しました。もう一度お試しください。';
            return view('hogoshanumber', compact('error'));
        }
    } else {
        $error = '受給者証番号と生年月日が一致する利用者が存在しません。<br>施設側でこのアプリにご家族の登録がされているか施設にお問い合わせください';
        return view('hogoshanumber', compact('error'));
    }

}

    public function store(Request $request)
    {
        // バリデーションルールとメッセージを定義
        $rules = [
            'jukyuusha_number' => 'required|digits:10',
            'date_of_birth' => 'required|date_format:Y-m-d',
        ];

        $messages = [
            'jukyuusha_number.required' => '受給者証番号は必須です。',
            'jukyuusha_number.digits' => '受給者証番号は10桁で入力してください。',
            'date_of_birth.required' => '生年月日は必須です。',
            'date_of_birth.date_format' => '生年月日は正しい形式で入力してください。',
        ];

        // バリデーションを実行
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return view('hogoshanumber', [
                'errors' => $validator->errors(),
                'input' => $request->all()
            ]);
        }

        // 受給者証番号と生年月日で人を検索
        $person = Person::where('jukyuusha_number', $request->jukyuusha_number)
                        ->where('date_of_birth', $request->date_of_birth)
                        ->first();

        if ($person) {
            // セッションから登録データを取得
            $userData = $request->session()->get('user_data');
            
            if (!$userData) {
                $error = 'セッションの登録データが見つかりませんでした。';
                return view('hogoshanumber', compact('error'));
            }

            try {
                DB::beginTransaction();

                $user = User::create([
                    'last_name' => $userData['last_name'],
                    'first_name' => $userData['first_name'],
                    'last_name_kana' => $userData['last_name_kana'],
                    'first_name_kana' => $userData['first_name_kana'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'terms_accepted_at' => $userData['terms_accepted_at'],
                    'privacy_accepted_at' => $userData['privacy_accepted_at'],
                ]);

                Auth::login($user);

                $user->people_family()->syncWithoutDetaching($person->id);
                $people = $user->people_family()->get();
                $user->assignRole('client family user');

                DB::commit();

                return view('hogosha', compact('people'));
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error($e->getMessage());
                $error = '登録処理中にエラーが発生しました。もう一度お試しください。';
                return view('hogoshanumber', compact('error'));
            }
        } else {
            $error = 'この受給者証番号の利用者が存在しません。<br>施設側でこのアプリにご家族の登録がされているか施設にお問い合わせください';
            return view('hogoshanumber', compact('error'));
        }
    }

    public function generateInviteUrl(Request $request)
    {
        try {
            $user = Auth::user();
            $person = People::findOrFail($request->person_id);
            
            // 署名付きURLの生成
            $signedUrl = URL::temporarySignedRoute(
                'hogosha.register',
                now()->addDays(1),
                [
                    'jukyuusha_number' => $person->jukyuusha_number,
                    'date_of_birth' => $person->date_of_birth,
                    'source' => 'line'
                ]
            );
            
            // スマートフォンの場合のLINE共有URL
            if ($request->header('User-Agent') && 
                (strpos($request->header('User-Agent'), 'Mobile') !== false || 
                 strpos($request->header('User-Agent'), 'Android') !== false || 
                 strpos($request->header('User-Agent'), 'iPhone') !== false)) {
                return response()->json([
                    'url' => 'line://msg/text/?' . urlencode($signedUrl)
                ]);
            }
            
            // PCの場合の通常のURL
            return response()->json([
                'url' => $signedUrl
            ]);
            
        } catch (\Exception $e) {
            \Log::error('URL生成エラー: ' . $e->getMessage());
            return response()->json([
                'error' => 'URLの生成に失敗しました'
            ], 400);
        }
    }
}
