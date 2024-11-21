<x-guest-layout>
    
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>登録画面</title>
</head>
@if (isset($error))
    <div style="color: red;">
        {!! $error !!}
    </div>
@endif

<body>

    <!--<form action="" method="post">-->
    <form action="{{ url('/hogosharegister') }}" method="post">
        @csrf
        <!-- Name -->
        <div class="mt-4 flex space-x-4">
                <div class="flex-1">
                    <x-input-label for="last_name" :value="__('姓')" />
                    <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autofocus autocomplete="last_name" />
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                </div>
                <div class="flex-1">
                    <x-input-label for="first_name" :value="__('名')" />
                    <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autocomplete="first_name" />
                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                </div>
            </div>

            <div class="mt-4 flex space-x-4">
                <div class="flex-1">
                    <x-input-label for="last_name_kana" :value="__('セイ')" />
                    <x-text-input id="last_name_kana" class="block mt-1 w-full" type="text" name="last_name_kana" :value="old('last_name_kana')" autocomplete="last_name_kana" />
                    <x-input-error :messages="$errors->get('last_name_kana')" class="mt-2" />
                </div>
                <div class="flex-1">
                    <x-input-label for="first_name_kana" :value="__('メイ')" />
                    <x-text-input id="first_name_kana" class="block mt-1 w-full" type="text" name="first_name_kana" :value="old('first_name_kana')" autocomplete="first_name_kana" />
                    <x-input-error :messages="$errors->get('first_name_kana')" class="mt-2" />
                </div>
            </div>
        
        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        
        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('パスワード（英語大文字小文字・数字を含む8文字以上）')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
        
        
        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('before-login') }}">
                {{ __('Already registered?') }}
            </a>
            
        <!--<button type="submit">送信</button>-->

            <x-primary-button class="ml-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
        
    </form>
</body>
</html>
</x-guest-layout>