<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $rules = [
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name_kana' => ['required', 'string', 'max:255', 'regex:/^[ァ-ヶー]+$/u'],
            'first_name_kana' => ['required', 'string', 'max:255', 'regex:/^[ァ-ヶー]+$/u'],
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->user()->id,
        ];

        // パスワードが入力された場合のみ、パスワード関連のルールを追加
        if ($request->filled('password')) {
            $rules['password'] = [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
            ];
            $rules['current_password'] = ['required'];
        }

        $form = $request->validate($rules, [
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
            'password.regex' => 'パスワードは大文字、小文字、数字を含む必要があります。',
            'current_password.required' => '現在のパスワードを入力してください。',
        ]);

        // 現在のパスワードが正しいか確認
        // if ($request->filled('password') && !Hash::check($request->input('current_password'), $request->user()->password)) {
        //     return back()->withErrors(['current_password' => '現在のパスワードが正しくありません。']);
        // }

        
    //     // 現在のパスワードが一致するかチェック
    //     if (!Hash::check($validated['current_password'], $request->user()->password)) {
    //         // 現在のパスワードが正しいかチェック
    //     // if (!Hash::check($value, auth()->user()->password)) {
    //     dd($validated['current_password'], $request->user()->password);
    //     return back()->withErrors([
    //         'current_password' => __('現在のパスワードが正しくありません。'),
    //     ], 'updatePassword');
    // }
        $user = $request->user();
        $user->fill($request->validated());
        $user->last_name = $request->input('last_name');
        $user->first_name = $request->input('first_name');
        $user->last_name_kana = $request->input('last_name_kana');
        $user->first_name_kana = $request->input('first_name_kana');

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();

        if ($user->hasAnyRole(['facility staff administrator', 'facility staff user', 'facility staff reader'])) {
            return Redirect::route('people.index')->with('success', 'プロフィールが正常に変更されました。');
        } elseif ($user->hasAnyRole(['client family user', 'client family reader'])) {
            return redirect('/hogosha')->with('success', 'プロフィールが正常に変更されました。');
        } else {
            // デフォルトのリダイレクト先（例：ダッシュボード）
            return Redirect::route('dashboard')->with('success', 'プロフィールが正常に変更されました。');
        }
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
