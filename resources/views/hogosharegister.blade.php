<x-guest-layout>
    
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>登録画面</title>
    <style>
        .large-label {
            font-size: 1.2em;
            font-weight: bold;
        }
        .error {
            color: red;
            font-size: 0.9em;
        }
    </style>
</head>
@if (isset($error))
    <div style="color: red;">
        {!! $error !!}
    </div>
@endif

<body>

    <!--<form action="" method="post">-->
    <form id="registerForm" action="{{ url('/hogosharegister') }}" method="post">
        @csrf
        <!-- Name -->
        <div class="mt-4 flex space-x-4">
        <div class="flex-1">
            <x-input-label for="last_name" :value="__('姓')" class="large-label" />
            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autofocus autocomplete="last_name" />
            <span id="last_name_error" class="error"></span>
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>
        <div class="flex-1">
            <x-input-label for="first_name" :value="__('名')" class="large-label" />
            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autocomplete="first_name" />
            <span id="first_name_error" class="error"></span>
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>
    </div>

    <div class="mt-4 flex space-x-4">
        <div class="flex-1">
            <x-input-label for="last_name_kana" :value="__('セイ(カナ)')" class="large-label" />
            <x-text-input id="last_name_kana" class="block mt-1 w-full" type="text" name="last_name_kana" :value="old('last_name_kana')" autocomplete="last_name_kana" />
            <span id="last_name_kana_error" class="error"></span>
            <x-input-error :messages="$errors->get('last_name_kana')" class="mt-2" />
        </div>
        <div class="flex-1">
            <x-input-label for="first_name_kana" :value="__('メイ(カナ)')" class="large-label" />
            <x-text-input id="first_name_kana" class="block mt-1 w-full" type="text" name="first_name_kana" :value="old('first_name_kana')" autocomplete="first_name_kana" />
            <span id="first_name_kana_error" class="error"></span>
            <x-input-error :messages="$errors->get('first_name_kana')" class="mt-2" />
        </div>
    </div>

    <!-- Email Address -->
    <div class="mt-4">
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
        <span id="email_error" class="error"></span>
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <!-- Password -->
    <div class="mt-4">
        <x-input-label for="password" :value="__('パスワード（英語大文字小文字・数字を含む8文字以上）')" />
        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
        <span id="password_error" class="error"></span>
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <!-- Confirm Password -->
    <div class="mt-4">
        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
        <span id="password_confirmation_error" class="error"></span>
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

    <script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const inputs = form.querySelectorAll('input');

    inputs.forEach(input => {
        input.addEventListener('input', function() {
            validateField(this);
        });
    });

    form.addEventListener('submit', function(e) {
        let isValid = true;
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });

        if (!isValid) {
            e.preventDefault();
        }
    });

    function validateField(field) {
        const errorSpan = document.getElementById(field.id + '_error');
        let isValid = true;

        switch(field.id) {
            case 'last_name':
            case 'first_name':
                if (field.value.trim() === '') {
                    errorSpan.textContent = field.id === 'last_name' ? '姓を入力してください。' : '名を入力してください。';
                    isValid = false;
                } else if (field.value.length > 255) {
                    errorSpan.textContent = '255文字以内で入力してください。';
                    isValid = false;
                } else {
                    errorSpan.textContent = '';
                }
                break;
            case 'last_name_kana':
            case 'first_name_kana':
                if (field.value.trim() === '') {
                    errorSpan.textContent = field.id === 'last_name_kana' ? 'セイを入力してください。' : 'メイを入力してください。';
                    isValid = false;
                } else if (field.value.length > 255) {
                    errorSpan.textContent = '255文字以内で入力してください。';
                    isValid = false;
                } else if (!/^[ァ-ヶー]+$/.test(field.value)) {
                    errorSpan.textContent = 'カタカナで入力してください。';
                    isValid = false;
                } else {
                    errorSpan.textContent = '';
                }
                break;
            case 'email':
                if (field.value.trim() === '') {
                    errorSpan.textContent = 'メールアドレスを入力してください。';
                    isValid = false;
                } else if (field.value.length > 255) {
                    errorSpan.textContent = 'メールアドレスは255文字以内で入力してください。';
                    isValid = false;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
                    errorSpan.textContent = '有効なメールアドレスを入力してください。';
                    isValid = false;
                } else {
                    errorSpan.textContent = '';
                }
                break;
            case 'password':
                if (field.value.trim() === '') {
                    errorSpan.textContent = 'パスワードを入力してください。';
                    isValid = false;
                } else if (field.value.length < 8) {
                    errorSpan.textContent = 'パスワードは8文字以上で入力してください。';
                    isValid = false;
                } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/.test(field.value)) {
                    errorSpan.textContent = 'パスワードは大文字、小文字、数字を含む必要があります。';
                    isValid = false;
                } else {
                    errorSpan.textContent = '';
                }
                break;
            case 'password_confirmation':
                if (field.value !== document.getElementById('password').value) {
                    errorSpan.textContent = 'パスワードが一致しません。';
                    isValid = false;
                } else {
                    errorSpan.textContent = '';
                }
                break;
        }

        return isValid;
    }
});
</script>
</body>
</html>
</x-guest-layout>