<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name_kana' => ['nullable', 'string', 'max:255', 'regex:/^[ァ-ヶー]+$/u'],
            'first_name_kana' => ['nullable', 'string', 'max:255', 'regex:/^[ァ-ヶー]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'custom_id' => [
                'required',
                'string',
                'max:20',
                'regex:/^[a-zA-Z0-9]+$/',
                'unique:users,custom_id', 
                'unique:'.User::class
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
            ],
        ], [
            'last_name.required' => '姓は必須です。',
            'first_name.required' => '名は必須です。',
            'last_name_kana.regex' => 'セイはカタカナで入力してください。',
            'first_name_kana.regex' => 'メイはカタカナで入力してください。',
            'email.required' => 'メールアドレスは必須です。',
            'email.email' => '有効なメールアドレスを入力してください。',
            'email.unique' => 'このメールアドレスは既に使用されています。',
            'custom_id.required' => 'IDは必須です。',
            'custom_id.max' => 'IDは20文字以内にしてください。',
            'custom_id.regex' => 'IDはアルファベットもしくは数字で登録してください。',
            'custom_id.unique' => 'このIDは既に使用されています。',
            'password.required' => 'パスワードは必須です。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'password.regex' => 'パスワードは英語大文字小文字・数字を含む必要があります。',
            'password.confirmed' => 'パスワードが一致しません。',
        ]);
        $user = User::create([
            'last_name' => $request['last_name'],
            'first_name' => $request['first_name'],
            'last_name_kana' => $request['last_name_kana'],
            'first_name_kana' => $request['first_name_kana'],
            'email' => $request->email,
            'custom_id' => $request->custom_id,
            'password' => Hash::make($request->password),

            'terms_accepted_at' => session('admin_terms_accepted_at'),
            'privacy_accepted_at' => session('admin_privacy_accepted_at'),
           'terms_accepted' => true,  // ここまでくる前に必ず同意をチェックしてるためrueを設定
           'privacy_accepted' => true,  // ここまでくる前に必ず同意をチェックしてるためrueを設定
            'terms_accepted_at' => session('terms_accepted_at'),
            'privacy_accepted_at' => session('privacy_accepted_at')
        ]);

        event(new Registered($user));

        Auth::login($user);
        // 職員が新規登録した後の遷移先をFACILITYREGISTER（facilityregister.blade.php）に指定
        return redirect(RouteServiceProvider::FACILITYREGISTER);
    }
}
