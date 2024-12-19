<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;

class CustomIDController extends Controller
{
   
        
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'custom_id' => [
                'required',
                'string',
                'max:20',
                'regex:/^[a-zA-Z0-9]+$/',
                'unique:users,custom_id', 
                'unique:'.User::class, 
                'confirmed'
              ],
              'custom_id_confirmation' => 'required',
          ], [
              'custom_id.required' => 'IDは必須です。',
              'custom_id.max' => 'IDは20文字以内にしてください。',
              'custom_id.regex' => 'IDはアルファベットもしくは数字で登録してください。',
              'custom_id.unique' => 'このIDは既に使用されています。',
              'custom_id.confirmed' => 'IDが一致しません。',
              'custom_id_confirmation.required' => '確認用IDは必須です。',
          ]);
    
        $request->user()->update([
            'custom_id' => ($validated['custom_id']),
        ]);

        $user = $request->user();

        // if ($user->hasAnyRole(['facility staff administrator', 'facility staff user', 'facility staff reader'])) {
            return Redirect::route('people.index')->with('success', 'IDが正常に変更されました。');
        // } elseif ($user->hasAnyRole(['client family user', 'client family reader'])) {
        //     return redirect('/hogosha')->with('success', 'パスワードが正常に変更されました。');
        // } else {
        //     // デフォルトのリダイレクト先（例：ダッシュボード）
        //     return Redirect::route('dashboard')->with('success', 'パスワードが正常に変更されました。');
        // }


    }
}
