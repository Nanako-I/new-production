<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Redirect;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    // public function update(Request $request): RedirectResponse
    // {
    //     // $validated = $request->validateWithBag('updatePassword', [
    //     $validated = $request->validate([
    //         'current_password' => ['required', 'current_password'],
    //         // 'password' => ['required', Password::defaults(), 'confirmed'],
    //         'password' => [
    //         'required',
    //         'string',
    //         'min:8',
    //         'confirmed',
    //         'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/' // '大文字小文字英数字含む,
    //     ],
    //     ]);
        
    //     // 現在のパスワードが一致するかチェック
    //     if (!Hash::check($validated['current_password'], $request->user()->password)) {
    //         // 現在のパスワードが正しいかチェック
    //     // if (!Hash::check($value, auth()->user()->password)) {
    //     dd($validated['current_password'], $request->user()->password);
    //     return back()->withErrors([
    //         'current_password' => __('現在のパスワードが正しくありません。'),
    //     ], 'updatePassword');
    // }
    
    //     $request->user()->update([
    //         'password' => Hash::make($validated['password']),
    //     ]);

    //     return back()->with('status', 'password-updated');
    // }
        
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/' // 大文字小文字英数字含む
            ],
        ], [
            'password.required' => 'パスワードを入力してください。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'password.confirmed' => 'パスワードが一致しません。',
            'password.regex' => 'パスワードは大文字、小文字、数字を含む必要があります。'
        ]);
    
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $user = $request->user();

        if ($user->hasAnyRole(['facility staff administrator', 'facility staff user', 'facility staff reader'])) {
            return Redirect::route('people.index')->with('success', 'パスワードが正常に変更されました。');
        } elseif ($user->hasAnyRole(['client family user', 'client family reader'])) {
            return redirect('/hogosha')->with('success', 'パスワードが正常に変更されました。');
        } else {
            // デフォルトのリダイレクト先（例：ダッシュボード）
            return Redirect::route('dashboard')->with('success', 'パスワードが正常に変更されました。');
        }


    }
}
