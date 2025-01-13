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
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;

use App\Models\User;
use App\Models\Facility;
use App\Models\Person;
use App\Models\Role;
use App\Models\Chat;
use Carbon\Carbon;
use App\Mail\RegistrationCompleted;

class HogoshaUserController extends Controller
{

    // public function showRegister()
    // {
    //     // セッションからpeople_idsを取得（複数のIDを想定）
    //     $people_ids = session('temp_people_ids', []);
    
    //     if (empty($people_ids)) {
    //         $error = 'People IDが見つかりません。';
    //         return view('hogosharegister', compact('error'));
    //     }
    
    //     // 複数のPersonを取得
    //     $people = Person::whereIn('id', $people_ids)->get();
    
    //     if ($people->isEmpty()) {
    //         $error = '指定されたIDに該当する人物が見つかりません。';
    //         return view('hogosharegister', compact('error'));
    //     }
    
    //     // 単一のPersonの場合の処理
    //     $people_id = session('temp_person_id');
    //     $person = Person::findOrFail($people_id);
    
    //     return view('hogosharegister', compact('people', 'person', 'people_ids'));
    // }
    public function showRegister()
    {
        // セッションからpersonIdを取得
        $people_id = session('temp_person_id');

        if (!$people_id) {
            $error = 'People IDが見つかりません。';
            return view('hogosharegister', compact('error'));
        }

        $person = Person::findOrFail($people_id);

        

        return view('hogosharegister', compact('person', 'people_id'));
    }

    
   
//     public function register(Request $request, $people_id)
// {
//     Log::info('Register method started', [
//         'user_agent' => $request->header('User-Agent'),
//         'ip_address' => $request->ip()
//     ]);

//     try {
//         Log::info('Starting validation');
//         $validatedData = $request->validate([
//             'last_name' => ['required', 'string', 'max:255'],
//             'first_name' => ['required', 'string', 'max:255'],
//             'last_name_kana' => ['required', 'string', 'max:255', 'regex:/^[ァ-ヶー]+$/u'],
//             'first_name_kana' => ['required', 'string', 'max:255', 'regex:/^[ァ-ヶー]+$/u'],
//             'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
//             'password' => [
//                 'required',
//                 'string',
//                 'min:8',
//                 'confirmed',
//                 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/' // 大文字小文字英数字含む
//             ]
//         ], [
//             'last_name.required' => '姓を入力してください。',
//             'last_name.max' => '姓は255文字以内で入力してください。',
//             'first_name.required' => '名を入力してください。',
//             'first_name.max' => '名は255文字以内で入力してください。',
//             'last_name_kana.required' => 'セイを入力してください。',
//             'last_name_kana.max' => 'セイは255文字以内で入力してください。',
//             'last_name_kana.regex' => 'セイはカタカナで入力してください。',
//             'first_name_kana.required' => 'メイを入力してください。',
//             'first_name_kana.max' => 'メイは255文字以内で入力してください。',
//             'first_name_kana.regex' => 'メイはカタカナで入力してください。',
//             'email.required' => 'メールアドレスを入力してください。',
//             'email.email' => '有効なメールアドレスを入力してください。',
//             'email.max' => 'メールアドレスは255文字以内で入力してください。',
//             'email.unique' => 'このメールアドレスは既に使用されています。',
//             'password.required' => 'パスワードを入力してください。',
//             'password.min' => 'パスワードは8文字以上で入力してください。',
//             'password.confirmed' => 'パスワードが一致しません。',
//             'password.regex' => 'パスワードは大文字、小文字、数字を含む必要があります。'
//         ]);
//         Log::info('Validation passed', $validatedData);

//         DB::beginTransaction();
//         Log::info('Starting database transaction');
//         $userData = $request->session()->get('user_data');

//         try {
//             $user = User::create([
//                 'last_name' => $validatedData['last_name'],
//                 'first_name' => $validatedData['first_name'],
//                 'last_name_kana' => $validatedData['last_name_kana'],
//                 'first_name_kana' => $validatedData['first_name_kana'],
//                 'email' => $validatedData['email'],
//                 'password' => Hash::make($validatedData['password']),
//                 'terms_accepted' => session('terms_accepted', false),
//                 'privacy_accepted' => session('privacy_accepted', false),
//                 'terms_accepted_at' => session('terms_accepted_at'),
//                 'privacy_accepted_at' => session('privacy_accepted_at')
//             ]);
//             Log::info('User created', ['user_id' => $user->id]);

//             // Get the person_id from the session and create the relationship
//             $personId = session('temp_person_id');
            
//             if ($personId) {
//                 $user->people_family()->syncWithoutDetaching([$personId]);
//                 $user->assignRole('client family user');
//             } else {
//                 Log::warning('No temp_person_id found in session during registration');
//             }

//             Auth::login($user);

//             DB::commit();
//             Log::info('Database transaction committed');

//             // Fetch unread messages
//             $unreadMessages = Chat::where('people_id', $personId)
//                 ->where('is_read', false)
//                 ->where('user_identifier', '!=', $user->id)
//                 ->exists();

//             $people = $user->people_family()->get();

//             return view('hogosha', compact('people', 'unreadMessages'));
//         } catch (\Exception $e) {
//             Log::error('Error during registration: ' . $e->getMessage());
//             DB::rollBack();
//             throw $e;
//         }
//     } catch (ValidationException $e) {
//         Log::error('Validation error: ' . json_encode($e->errors()));
//         return back()->withErrors($e->errors())->withInput();
//     } catch (\Exception $e) {
//         Log::error('Unexpected error during registration: ' . $e->getMessage());
//         return back()->withErrors(['email' => '登録処理中にエラーが発生しました。もう一度お試しください。'])->withInput();
//     }

//     Log::info('Register method completed');
// }
// public function register(Request $request, $people_id)
// {
//     Log::info('Register method started');

//     try {
//         Log::info('Starting validation');
//         $validatedData = $request->validate([
//             'last_name' => ['required', 'string', 'max:255'],
//             'first_name' => ['required', 'string', 'max:255'],
//             'last_name_kana' => ['required', 'string', 'max:255', 'regex:/^[ァ-ヶー]+$/u'],
//             'first_name_kana' => ['required', 'string', 'max:255', 'regex:/^[ァ-ヶー]+$/u'],
//             'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
//             'password' => [
//                 'required',
//                 'string',
//                 'min:8',
//                 'confirmed',
//                 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/' // 大文字小文字英数字含む
//             ]
//         ], [
//             'last_name.required' => '姓を入力してください。',
//             'last_name.max' => '姓は255文字以内で入力してください。',
//             'first_name.required' => '名を入力してください。',
//             'first_name.max' => '名は255文字以内で入力してください。',
//             'last_name_kana.required' => 'セイを入力してください。',
//             'last_name_kana.max' => 'セイは255文字以内で入力してください。',
//             'last_name_kana.regex' => 'セイはカタカナで入力してください。',
//             'first_name_kana.required' => 'メイを入力してください。',
//             'first_name_kana.max' => 'メイは255文字以内で入力してください。',
//             'first_name_kana.regex' => 'メイはカタカナで入力してください。',
//             'email.required' => 'メールアドレスを入力してください。',
//             'email.email' => '有効なメールアドレスを入力してください。',
//             'email.max' => 'メールアドレスは255文字以内で入力してください。',
//             'email.unique' => 'このメールアドレスは既に使用されています。',
//             'password.required' => 'パスワードを入力してください。',
//             'password.min' => 'パスワードは8文字以上で入力してください。',
//             'password.confirmed' => 'パスワードが一致しません。',
//             'password.regex' => 'パスワードは大文字、小文字、数字を含む必要があります。'
//         ]);
//         Log::info('Validation passed', $validatedData);

//         DB::beginTransaction();
//         Log::info('Starting database transaction');

//         try {
//             $user = User::create([
//                 'last_name' => $validatedData['last_name'],
//                 'first_name' => $validatedData['first_name'],
//                 'last_name_kana' => $validatedData['last_name_kana'],
//                 'first_name_kana' => $validatedData['first_name_kana'],
//                 'email' => $validatedData['email'],
//                 'password' => Hash::make($validatedData['password']),
//                 'terms_accepted' => session('terms_accepted', false),
//                 'privacy_accepted' => session('privacy_accepted', false),
//                 'terms_accepted_at' => session('terms_accepted_at'),
//                 'privacy_accepted_at' => session('privacy_accepted_at')
//             ]);
//             Log::info('User created', ['user_id' => $user->id]);

//             // Get the person_id from the session and create the relationship
//             $personId = session('temp_person_id');
            
//             if ($personId) {
//                 $user->people_family()->syncWithoutDetaching([$personId]);
//                 $user->assignRole('client family user');
//             } else {
//                 Log::warning('No temp_person_id found in session during registration');
//             }

//             Auth::login($user);

//             DB::commit();
//             Log::info('Database transaction committed');

//             // Fetch unread messages
//             $unreadMessages = Chat::where('people_id', $personId)
//                 ->where('is_read', false)
//                 ->where('user_identifier', '!=', $user->id)
//                 ->exists();

//             $people = $user->people_family()->get();

//             return view('hogosha', compact('people', 'unreadMessages'));
//         } catch (\Exception $e) {
//             Log::error('Error during registration: ' . $e->getMessage());
//             DB::rollBack();
//             throw $e;
//         }
//     } catch (ValidationException $e) {
//         Log::error('Validation error: ' . json_encode($e->errors()));
//         return back()->withErrors($e->errors())->withInput();
//     } catch (\Exception $e) {
//         Log::error('Unexpected error during registration: ' . $e->getMessage());
//         return back()->withErrors(['email' => '登録処理中にエラーが発生しました。もう一度お試しください。'])->withInput();
//     }

//     Log::info('Register method completed');
// }
public function register(Request $request)
    {
        $form = $request->validate([
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name_kana' => ['required', 'string', 'max:255', 'regex:/^[ァ-ヶー]+$/u'],
            'first_name_kana' => ['required', 'string', 'max:255', 'regex:/^[ァ-ヶー]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
            ]
        ], [
            'last_name.required' => '姓を入力してください。',
            'last_name.max' => '姓は255文字以内で入力してください。',
            'first_name.required' => '名を入力してください。',
            'first_name.max' => '名は255文字以内で入力してください。',
            'last_name_kana.required' => 'セイを入力してください。',
            'last_name_kana.max' => 'セイは255文字以内で入力してください。',
            'last_name_kana.regex' => 'セイはカタカナで入力してください。',
            'first_name_kana.required' => 'メイを入力してください。',
            'first_name_kana.max' => 'メイは255文字以内で入力してください。',
            'first_name_kana.regex' => 'メイはカタカナで入力してください。',
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => '有効なメールアドレスを入力してください。',
            'email.max' => 'メールアドレスは255文字以内で入力してください。',
            'email.unique' => 'このメールアドレスは既に使用されています。',
            'password.required' => 'パスワードを入力してください。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'password.confirmed' => 'パスワードが一致しません。',
            'password.regex' => 'パスワードは大文字、小文字、数字を含む必要があります。'
        ]);

        // 入力データと同意情報を配列に保存
        $now = Carbon::now();
        $userData = [
            'last_name' => $request->input('last_name'),
            'first_name' => $request->input('first_name'),
            'last_name_kana' => $request->input('last_name_kana'),
            'first_name_kana' => $request->input('first_name_kana'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'terms_accepted' => true,
            'privacy_accepted' => true,
            'terms_accepted_at' => $now,
            'privacy_accepted_at' => $now
        ];

        $request->session()->put('user_data', $userData);

        $personId = session('temp_person_id');

        return view('hogoshanumber', compact('userData','personId'));
    }
// public function register(Request $request, $people_id)
// {
//     // try {
//         $validated = $request->validate([
//             'last_name' => ['required', 'string', 'max:255'],
//             'first_name' => ['required', 'string', 'max:255'],
//             'last_name_kana' => ['required', 'string', 'max:255', 'regex:/^[ァ-ヶー]+$/u'],
//             'first_name_kana' => ['required', 'string', 'max:255', 'regex:/^[ァ-ヶー]+$/u'],
//             'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
//             'password' => [
//                 'required',
//                 'string',
//                 'min:8',
//                 'confirmed',
//                 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
//             ],
//         ], [
//             'last_name.required' => '姓を入力してください。',
//             'last_name.max' => '姓は255文字以内で入力してください。',
//             'first_name.required' => '名を入力してください。',
//             'first_name.max' => '名は255文字以内で入力してください。',
//             'last_name_kana.required' => 'セイを入力してください。',
//             'last_name_kana.max' => 'セイは255文字以内で入力してください。',
//             'last_name_kana.regex' => 'セイはカタカナで入力してください。',
//             'first_name_kana.required' => 'メイを入力してください。',
//             'first_name_kana.max' => 'メイは255文字以内で入力してください。',
//             'first_name_kana.regex' => 'メイはカタカナで入力してください。',
//             'email.required' => 'メールアドレスを入力してください。',
//             'email.email' => '有効なメールアドレスを入力してください。',
//             'email.max' => 'メールアドレスは255文字以内で入力してください。',
//             'email.unique' => 'このメールアドレスは既に使用されています。',
//             'password.required' => 'パスワードを入力してください。',
//             'password.min' => 'パスワードは8文字以上で入力してください。',
//             'password.confirmed' => 'パスワードが一致しません。',
//             'password.regex' => 'パスワードは大文字、小文字、数字を含む必要があります。'
//         ]);

//         $userData = [
//                  'last_name' => $request->input('last_name'),
//      'first_name' => $request->input('first_name'),
//      'last_name_kana' => $request->input('last_name_kana'),
//      'first_name_kana' => $request->input('first_name_kana'),
//      'email' => $request->input('email'),
//     //  'password' => Hash::make($request->input('password')),
//     'password' => $request->input('password'),
//         ];

//         $request->session()->put('user_data', $userData);
//         $userData = session('user_data');
//         if ($request->ajax()) {
//             return response()->json([
//                 'success' => true,
//                 'message' => '登録情報が保存されました。',
//                 'redirect' => route('hogoshanumber.show')
//             ]);
//         }

//         return redirect()->route('hogoshanumber.show');

    // } catch (ValidationException $e) {
    //     if ($request->ajax()) {
    //         return response()->json([
    //             'success' => false,
    //             'errors' => $e->errors()
    //         ], 422);
    //     }
    //     return back()->withErrors($e->errors())->withInput();
    // } catch (\Exception $e) {
    //     \Log::error('Registration error: ' . $e->getMessage());
    //     if ($request->ajax()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => '登録処理中にエラーが発生しました。'
    //         ], 500);
    //     }
    //     return back()->with('error', '登録処理中にエラーが発生しました。')->withInput();
    // }
// }
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'last_name' => 'required|string|max:255',
    //         'first_name' => 'required|string|max:255',
    //         'last_name_kana' => 'required|string|max:255|regex:/^[ァ-ヶー]+$/u',
    //         'first_name_kana' => 'required|string|max:255|regex:/^[ァ-ヶー]+$/u',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    //     }

    //     $userData = $validator->validated();
    //     $request->session()->put('user_data', $userData);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'ユーザー情報が正常に保存されました。',
    //         'userData' => $userData
    //     ]);
    // }




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
    Log::info('numberregister メソッド開始');
    Log::info('リクエストデータ: ' . json_encode($request->all()));
      // バリデーションルールとメッセージを定義
    $rules = [
        // 'jukyuusha_number' => 'required|digits:10',
        'date_of_birth' => 'required|date_format:Y-m-d', // 必要に応じてフォーマットを調整
    ];

    $messages = [
        // 'jukyuusha_number.required' => '受給者証番号は必須です。',
        // 'jukyuusha_number.digits' => '受給者証番号は10桁で入力してください。',
        'date_of_birth.required' => '生年月日は必須です。',
        'date_of_birth.date_format' => '生年月日は正しい形式で入力してください。',
    ];

    // 試行回数を取得（セッションになければ0を設定）
    $attempts = session('birth_date_attempts', 0);

    // 試行回数が3回以上の場合
    if ($attempts >= 3) {
        // セッションをクリア
        session()->forget(['birth_date_attempts', 'user_data', 'temp_person_id']);
        
        // シンプルなエラーメッセージを白い画面に表示
        return response()->view('errorsbirthday', [
            'message' => '生年月日の入力を3回間違えました。最初から試してください。'
        ]);
    }

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
    Log::info('クエリ実行前');
    $person = Person::withoutGlobalScopes()
    // ->where('jukyuusha_number', $request->jukyuusha_number)
    ->where('date_of_birth', $request->date_of_birth)
    ->first();
Log::info('クエリ結果: ' . ($person ? 'データあり' : 'データなし'));

// 人が見つからなかった場合
if (!$person) {
    // 試行回数を増やす
    $attempts++;
    session(['birth_date_attempts' => $attempts]);

    // 試行回数が3回以上の場合
    if ($attempts >= 3) {
        // セッションをクリア
        session()->forget(['birth_date_attempts', 'user_data', 'temp_person_id']);
        
        // 生年月日を3回間違えたらエラー画面に遷移
        return response()->view('eroorsbirthday', [
            'message' => '生年月日が一致する利用者が存在しません。施設側でご家族の生年月日がこのアプリに正しく登録されているかお問い合わせください。'
        ]);
    }

    $error = '生年月日が一致する利用者が存在しません。<br>施設側でご家族の生年月日がこのアプリに正しく登録されているかお問い合わせください';
    return view('hogoshanumber', compact('error'));
}

    // Log::info('検索された人物: ' . ($person ? json_encode($person) : 'なし'));
    // 人が見つかった場合
        if ($person) {
            // セッションから登録データを取得
            $registerData = $request->session()->get('user_data');
            Log::info('セッションデータ: ' . json_encode($registerData));
            
            if (!$registerData) {
            // throw new \Exception('セッションの登録データが見つかりませんでした。');
            $error = '登録処理中にエラーが発生しました。お手数ですが再度ご登録をお試しください。';
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
            Log::info('ユーザー作成後: ' . json_encode($user));
            Auth::login($user);

            $user->people_family()->syncWithoutDetaching($person->id);
            $people = $user->people_family()->get();
            $user->assignRole('client family user');

            DB::commit();
            Log::info('登録成功: ユーザーID ' . $user->id);
            // 未読メッセージを取得
            $unreadMessages = Chat::where('people_id', $person->id)
            ->where('is_read', false)
            ->where('user_identifier', '!=', $user->id)
            ->exists();
            // dd($user->email);
            // メール送信 
            try {
            Mail::to($user->email)->send(new RegistrationCompleted($user));
            // メール送信が成功した場合のログ
            Log::info('メールが正常に送信されました。', ['email' => $user->email]);
        } catch (\Exception $e) {
            // メール送信が失敗した場合のログ
            Log::error('メール送信に失敗しました。', ['email' => $user->email, 'error' => $e->getMessage()]);
        }
            // hogosha ビューにデータを渡して表示
            session()->flash('success', 'ご登録ありがとうございます。<br>メールにて今後こちらのアプリへのログインのURLをお送りしておりますのでご確認ください。');
return response()->view('hogosha', compact('people', 'unreadMessages'));
           
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('登録処理中のエラー: ' . $e->getMessage());
            // Log::error('エラーの詳細: ' . $e->getTraceAsString());
            // ユーザーが既に存在する場合は、hogoshaビューにリダイレクト
        $existingUser = User::where('email', $registerData['email'])->first();
        if ($existingUser) {
            Auth::login($existingUser);
            $people = $existingUser->people_family()->get();
            $unreadMessages = Chat::where('people_id', $person->id)
                ->where('is_read', false)
                ->where('user_identifier', '!=', $existingUser->id)
                ->exists();
            
            // セッションをクリア
            session()->forget(['birth_date_attempts', 'user_data', 'temp_person_id']);
            session()->flash('success', 'ご登録ありがとうございます。<br>メールにて今後こちらのアプリへのログインのURLをお送りしておりますのでご確認ください。');
            return response()->view('hogosha', compact('people', 'unreadMessages'));
        }

        $error = '登録処理中にエラーが発生しました。お手数ですが再度ご登録をお試しください。';
        return view('hogoshanumber', compact('error'));
    }
}}

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