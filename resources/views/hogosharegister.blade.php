<x-guest-layout>
    
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        .modal {
            display: none; /* デフォルトでは非表示 */
            position: fixed; /* 固定位置 */
            top: 50%; /* 上から50% */
            left: 50%; /* 左から50% */
            transform: translate(-50%, -50%); /* 中央に配置 */
            z-index: 1000; /* 他の要素の上に表示 */
            background-color: white; /* 背景色 */
            padding: 30px; /* 内側の余白を増やす */
            width: 400px; /* モーダルの幅を広げる */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* 影 */
            border-radius: 8px; /* 角を丸く */
        }

        .modal-content {
            text-align: center; /* テキストを中央揃え */
        }

        button {
            margin: 10px; /* ボタン間の余白 */
            padding: 10px 20px; /* ボタンの内側の余白 */
            border: none; /* ボーダーを削除 */
            border-radius: 5px; /* ボタンの角を丸く */
            cursor: pointer; /* カーソルをポインターに */
        }

        
        #confirmButton {
            background-color: #007BFF; /* 鮮やかな青 */
            color: white; /* テキストの色 */
        }

        #cancelButton {
            background-color: #FF4136; /* 鮮やかな赤 */
            color: white; /* テキストの色 */
        }

        .modal-overlay {
            display: none; /* デフォルトでは非表示 */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* 薄いグレー */
            z-index: 999; /* モーダルの下に表示 */
        }

        .error-message {
            color: #f00; /* 赤色 */
            font-weight: bold; /* 太字 */
            font-size: 1.1em; /* 少し大きく */
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
    <!-- Rest of the form content -->
    <form id="registerForm" method="POST" action="{{ route('hogosharegister.store') }}">
    @csrf
        <p class="text-xl">あなたの氏名・メールアドレス・パスワードを登録してください</p>
      
        @if(isset($people_ids))
    <div class="mb-4">
        <h3 class="text-lg font-semibold mb-2">People ID(s):</h3>
        <ul class="list-disc list-inside">
            @foreach($people_ids as $id)
                <li class="text-green-600">{{ $id }}</li>
            @endforeach
        </ul>
    </div>
@endif
                <!-- @if(session('terms_accepted') && session('privacy_accepted'))
    <p>利用規約に同意しました: {{ session('terms_accepted_at') }}</p>
    <p>プライバシーポリシーに同意しました: {{ session('privacy_accepted_at') }}</p>
@endif   -->
        <!-- Name -->
        <div class="mt-4 flex space-x-4">      
        <div class="flex-1">
            <x-input-label for="last_name" :value="__('姓')" class="large-label" />
            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autofocus autocomplete="last_name" />
            <span id="last_name_error" class="error"></span>
            <x-input-error :messages="$errors->get('last_name')" class="error-message" />
        </div>
        <div class="flex-1">
            <x-input-label for="first_name" :value="__('名')" class="large-label" />
            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autocomplete="first_name" />
            <span id="first_name_error" class="error"></span>
            <x-input-error :messages="$errors->get('first_name')" class="error-message" />
        </div>
    </div>

    <div class="mt-4 flex space-x-4">
        <div class="flex-1">
            <x-input-label for="last_name_kana" :value="__('セイ(カナ)')" class="large-label" />
            <x-text-input id="last_name_kana" class="block mt-1 w-full" type="text" name="last_name_kana" :value="old('last_name_kana')" autocomplete="last_name_kana" />
            <span id="last_name_kana_error" class="error-message"></span>
            <x-input-error :messages="$errors->get('last_name_kana')" class="error-message" />
        </div>
        <div class="flex-1">
            <x-input-label for="first_name_kana" :value="__('メイ(カナ)')" class="large-label" />
            <x-text-input id="first_name_kana" class="block mt-1 w-full" type="text" name="first_name_kana" :value="old('first_name_kana')" autocomplete="first_name_kana" />
            <span id="first_name_kana_error" class="error-message"></span>
            <x-input-error :messages="$errors->get('first_name_kana')" class="error-message" />
        </div>
    </div>

    <!-- Email Address -->
    <div class="mt-4">
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
        <span id="email_error" class="error-message"></span>
        <x-input-error :messages="$errors->get('email')" class="error-message" />
    </div>

    <!-- Password -->
    <div class="mt-4">
        <x-input-label for="password" :value="__('パスワード（アルファベット大文字小文字・数字を含む8文字以上）')" />
        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
        <span id="password_error" class="error-message"></span>
        <x-input-error :messages="$errors->get('password')" class="error-message" />
    </div>

    <!-- Confirm Password -->
    <div class="mt-4">
        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
        <span id="password_confirmation_error" class="error-message"></span>
        <x-input-error :messages="$errors->get('password_confirmation')" class="error-message" />
    </div>
        
        
        <button type="submit" id="registerButton" class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-lg text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                送信
        </button>
        
    </form>

    <!-- モーダルの背景 -->
    <div id="modalOverlay" class="modal-overlay"></div>

    <!-- モーダルのHTML -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <p id="confirmationMessage" class="text-lg mb-4"></p>
            <div class="flex justify-end">
                <button id="confirmButton" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">はい</button>
                <button id="cancelButton" class="bg-gray-500 text-white px-4 py-2 rounded">いいえ</button>
            </div>
        </div>
    </div>

    

    <script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const inputs = form.querySelectorAll('input');
    const submitButton = form.querySelector('button[type="submit"]');

    inputs.forEach(input => {
        input.addEventListener('input', function() {
            validateField(this);
        });
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (validateForm()) {
            submitForm();
        }
    });

    function validateForm() {
        let isValid = true;
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });
        return isValid;
    }

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

//     function submitForm() {
//         submitButton.disabled = true;

//         const formData = new FormData(form);

//         fetch(form.action, {
//             method: 'POST',
//             body: formData,
//             headers: {
//                 'X-Requested-With': 'XMLHttpRequest',
//                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
//             }
//         })
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 alert('登録情報が保存されました。');
//                 window.location.href = data.redirect;
//             } else {
//                 displayErrors(data.errors);
//             }
//         })
//         .catch(error => {
//             console.error('エラー:', error);
//             alert('登録処理中にエラーが発生しました。もう一度お試しください。');
//         })
//         .finally(() => {
//             submitButton.disabled = false;
//         });
//     }

//     function displayErrors(errors) {
//         document.querySelectorAll('.error').forEach(el => el.textContent = '');
//         for (const [field, messages] of Object.entries(errors)) {
//             const errorSpan = document.getElementById(`${field}_error`);
//             if (errorSpan) {
//                 errorSpan.textContent = messages[0];
//             }
//         }
//     }
});

document.getElementById('registerButton').addEventListener('click', function() {
    // モーダルメッセージを設定
    const personName = "{{ $person->last_name }} {{ $person->first_name }}";
    document.getElementById('confirmationMessage').textContent = `${personName}さんの親御さんですか？`;

    // モーダルを表示
    document.getElementById('confirmationModal').classList.remove('hidden');
});

function handleConfirm() {
    // モーダルを非表示にしてフォームを送信
    document.getElementById('confirmationModal').classList.add('hidden');
    document.getElementById('registerForm').submit();
}

document.getElementById('confirmButton').addEventListener('click', handleConfirm);
document.getElementById('confirmButton').addEventListener('touchend', handleConfirm);

document.getElementById('cancelButton').addEventListener('click', function() {
    // モーダルを非表示
    document.getElementById('confirmationModal').classList.add('hidden');
    
    // 新しい画面を表示
    document.body.innerHTML = '<div class="flex items-center justify-center h-screen"><p class="text-xl">施設管理者にURLの再送を依頼してください</p></div>';
});
// const userData = @json(session('user_data', []));
// console.log(userData);
// document.getElementById('registerForm').addEventListener('submit', function(event) {
//     event.preventDefault(); // フォームのデフォルトの送信を防ぐ

//     // セッションデータをアラートで表示
//     if (userData) {
//         alert(`姓: ${userData.last_name}\n名: ${userData.first_name}\nメール: ${userData.email}`);
//     } else {
//         alert('セッションデータが見つかりません。');
//     }

    // フォームを送信する場合は以下の行をコメントアウト解除
    // this.submit();
// });
document.getElementById('registerForm').addEventListener('submit', function(event) {
    event.preventDefault(); // フォーム送信を一時停止

    // モーダルと背景を表示
    document.getElementById('confirmationModal').style.display = 'block';
    document.getElementById('modalOverlay').style.display = 'block';
});

document.getElementById('confirmButton').addEventListener('click', function() {
    // モーダルと背景を閉じてフォームを送信
    document.getElementById('confirmationModal').style.display = 'none';
    document.getElementById('modalOverlay').style.display = 'none';
    document.getElementById('registerForm').submit();
});

document.getElementById('cancelButton').addEventListener('click', function() {
    // モーダルと背景を閉じる
    document.getElementById('confirmationModal').style.display = 'none';
    document.getElementById('modalOverlay').style.display = 'none';
});

// オーバーレイをクリックしたときにモーダルを閉じる
document.getElementById('modalOverlay').addEventListener('click', function() {
    document.getElementById('confirmationModal').style.display = 'none';
    document.getElementById('modalOverlay').style.display = 'none';
});


</script>
</body>
</html>
</x-guest-layout>