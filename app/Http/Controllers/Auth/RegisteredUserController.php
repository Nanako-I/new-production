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
            'last_name_kana' => ['nullable', 'string', 'max:255'],
            'first_name_kana' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'custom_id' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/' // '大文字小文字英数字含む,
        ],
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
