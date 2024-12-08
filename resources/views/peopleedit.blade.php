<!-- resources/views/edit_person.blade.php -->
@vite(['resources/css/app.css', 'resources/js/app.js'])
<x-app-layout>

    <!--ヘッダー[START]-->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight" style="width: 100%;">
            {{ __('利用者情報の修正') }}
        </h2>
    </x-slot>
    <!--ヘッダー[END]-->

    <!-- バリデーションエラーの表示 -->
    @if ($errors->any())
        <div class="flex justify-between p-4 items-center bg-red-500 text-white rounded-lg border-2 border-white">

        
            @if ($errors->has('name') || $errors->has('date_of_birth'))
                <div><strong>氏名・生年月日は入力必須です。</strong></div> 
            @endif
            @if ($errors->has('duplicate_name_dob'))
                <div><strong>{{ $errors->first('duplicate_name_dob') }}</strong></div>
            @endif
            @if ($errors->has('jukyuusha_number'))
            <div class="mb-2"><strong>{{ $errors->first('jukyuusha_number') }}</strong></div>
        @endif
            
        </div>
    @endif

    <body class="h-full w-full">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <script src="https://kit.fontawesome.com/de653d534a.js" crossorigin="anonymous"></script>
    <div class="flex justify-end "> 
              <div class="flex-col"> 
                <p class="font-bold text-lg">連絡帳</p>
                <a href="{{ url('record/'.$person->id.'/edit') }}" class="relative ml-2" style="display: flex; align-items: center;">
                    <i class="fa-regular fa-clipboard text-slate-600 hover:text-slate-900 icon-container mr-5 " style="font-size: 3em; padding: 0 5px; transition: transform 0.2s;"></i>
                    @csrf
                  </a>
              </div> 
            

        <p class="font-bold text-lg">ご家族に新規登録のご案内を送る</p>
        <div class="share-buttons flex justify-center space-x-4 mt-4">
            
            <button type="button" 
                    id="share-line"
                    class="text-white bg-green-500 p-2 rounded flex items-center justify-center">
                <i class="fab fa-line text-3xl"></i>
            </button>
        </div>

        <div class="form-group mb-4 m-2 w-1/2 max-w-md md:w-1/6" style="display: flex; flex-direction: column; align-items: center;">
            <label class="block text-lg font-bold text-gray-700">LINE アカウント連携</label>
            <button type="button" id="link-line-account" class="mt-2 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                LINE アカウントを連携する
            </button>
        </div>
    </div> 

    <!-- 修正フォーム -->
    <form action="{{ route('people.update', $person->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

    <div style="display: flex; align-items: center; justify-content: center; flex-direction: column;">
   
    
        <div class="form-group mb-4 m-2 w-1/2 max-w-md md:w-1/6" style="display: flex; flex-direction: column; align-items: center;">
            <label class="block text-lg font-bold text-gray-700">名前</label>
            <input name="last_name" type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-xl font-bold border-gray-300 rounded-md" value="{{ old('last_name', $person->last_name) }}"placeholder="姓">
            <input name="first_name" type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-xl font-bold border-gray-300 rounded-md" value="{{ old('first_name', $person->first_name) }}" placeholder="名">

        </div>

        <div class="form-group mb-4 m-2 w-1/2 max-w-md md:w-1/6" style="display: flex; flex-direction: column; align-items: center;">
            <input name="last_name_kana" type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-xl font-bold border-gray-300 rounded-md" value="{{ old('last_name_kana', $person->last_name_kana) }}" placeholder="セイ">
            @error('last_name_kana')
                <div class="kana-error text-red-500 text-base font-bold mt-1">{{ $message }}</div>
            @enderror

            <input name="first_name_kana" type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-xl font-bold border-gray-300 rounded-md" value="{{ old('first_name_kana', $person->first_name_kana) }}" placeholder="メイ">
            @error('first_name_kana')
                <div class="kana-error text-red-500 text-base font-bold mt-1">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group mb-4 m-2 w-1/2 max-w-md md:w-1/6" style="display: flex; flex-direction: column; align-items: center;">
            <label class="block text-lg font-bold text-gray-700">生年月日</label>
            <input name="date_of_birth" type="date" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-xl font-bold border-gray-300 rounded-md" value="{{ old('date_of_birth', $person->date_of_birth) }}" placeholder="生年月日">
        </div>
        
        

      <div class="form-group mb-4 m-2 max-w-lg" style="display: flex; flex-direction: column; align-items: center;">
            <label class="block text-lg font-bold text-gray-700">受給者証番号（数字10桁）</label>
            <div class="border-2 border-gray-300 rounded-md p-2 mb-4 w-full">
                <input type="text" id="jukyuusha_number" name="jukyuusha_number" 
                    class="w-full text-center text-2xl font-bold tracking-widest" 
                    maxlength="10" placeholder="0123456789" value="{{ old('jukyuusha_number', $person->jukyuusha_number) }}" 
                    style="letter-spacing: 0.25em;">
            </div>
            <div id="number-error" class="text-red-500 text-base font-bold hidden"></div>
        </div>

@php
    $medicalCareNeedId = $facility->medicalCareNeeds()->first()->id ?? null;
@endphp

@if($medicalCareNeedId && in_array($medicalCareNeedId, [1, 2]))
    <div class="form-group mb-4 m-2 w-1/2 max-w-md md:w-1/6" style="display: flex; flex-direction: column; align-items: center;">
        <label class="block text-lg font-bold text-gray-700">医療的ケア</label>
        <input name="medical_care" type="checkbox" value="1" class="mt-1" {{ $person->medical_care ? 'checked' : '' }}>
        <span class="text-gray-500">医療的ケアを必要とする場合はチェックしてください</span>
    </div>
@endif


            <div class="form-group mb-4 m-2 w-1/2 max-w-md md:w-1/6" style="display: flex; flex-direction: column; align-items: center;">
                <label class="block text-lg font-bold text-gray-700">プロフィール画像</label>
                <input name="filename" id="filename" type="file" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-lg border-gray-300 rounded-md ml-20">
                @if ($person->filename)
                
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $person->filename) }}" alt="プロフィール画像" style="max-width: 150px;">
                    </div>
                @endif
            </div>

            <div class="flex flex-col col-span-1">
                <div class="text-gray-700 text-center px-4 py-2 m-2">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-lg text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        更新する
                    </button>
                </div>
            </div>
        </div>
    </form>
    <script src="https://unpkg.com/vue@3.2.47/dist/vue.global.prod.js"></script>
 <!--jquery3.6.4をCDN経由で呼び出し↓-->
 <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>

 <script>
    document.addEventListener('DOMContentLoaded', function() {
    const lastNameKana = document.querySelector('input[name="last_name_kana"]');
    const firstNameKana = document.querySelector('input[name="first_name_kana"]');
    const kanaFields = [lastNameKana, firstNameKana];

    function validateKana(input) {
        const kanaRegex = /^[ァ-ヶー]*$/;
        let errorDiv = input.nextElementSibling;
        
        if (!errorDiv || !errorDiv.classList.contains('kana-error')) {
            errorDiv = document.createElement('div');
            errorDiv.classList.add('kana-error', 'text-red-500', 'text-base', 'font-bold','mt-1');
            input.parentNode.insertBefore(errorDiv, input.nextSibling);
        }

        if (!kanaRegex.test(input.value) && input.value !== '') {
            errorDiv.textContent = 'カタカナのみを入力してください。';
            input.classList.add('border-red-500');
        } else {
            errorDiv.textContent = '';
            input.classList.remove('border-red-500');
        }
    }

    kanaFields.forEach(field => {
        field.addEventListener('input', function() {
            validateKana(this);
        });
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        kanaFields.forEach(field => {
            validateKana(field);
            if (field.nextElementSibling && field.nextElementSibling.textContent) {
                e.preventDefault();
            }
        });
    });
});

    document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('jukyuusha_number');
    const errorDiv = document.getElementById('number-error');

    input.addEventListener('input', function(e) {
        // 数字以外の文字を削除
        this.value = this.value.replace(/[^0-9]/g, '');

        // 10桁を超える入力を防ぐ
        if (this.value.length > 10) {
            this.value = this.value.slice(0, 10);
        }

        // エラーメッセージの表示/非表示
        if (this.value.length === 10) {
            errorDiv.classList.add('hidden');
        } else {
            errorDiv.textContent = '受給者証番号は10桁の数字で入力してください。';
            errorDiv.classList.remove('hidden');
        }
    });

        // フォーム送信時のバリデーション
        document.querySelector('form').addEventListener('submit', function(e) {
            if (input.value.length !== 10) {
                e.preventDefault();
                errorDiv.textContent = '受給者証番号は10桁の数字で入力してください。';
                errorDiv.classList.remove('hidden');
            }
        });
    });

    document.getElementById('filename').addEventListener('click', function() {
        // 選択されたファイルに対する処理を追加する（例: アップロード処理など）
        console.log('ファイルが選択されました:', this.files[0].name);
    });

    
    document.addEventListener('DOMContentLoaded', function () {
        const shareUrl = "{!! $url !!}";
        console.log('Share URL:', shareUrl);

        const defaultMessage = "新連絡帳システムのご案内です。以下のリンクから新規登録してください。有効期限は本メッセージ送信後24時間以内となります。有効期限切れの場合は施設管理者に再送をご依頼ください:";

        document.getElementById('share-line').addEventListener('click', function (e) {
            e.preventDefault();
            if (!shareUrl) {
                console.error('No URL available for sharing');
                alert('共有するURLがありません。管理者に連絡してください。');
                return;
            }

            const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
            if (isMobile) {
                window.location.href = `line://msg/text/${encodeURIComponent(defaultMessage + "\n" + shareUrl)}`;
            } else {
                const lineShareUrl = `https://social-plugins.line.me/lineit/share?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(defaultMessage)}`;
                window.open(lineShareUrl, '_blank');
            }
            console.log('LINE share button clicked');
        });
    });
    document.getElementById('link-line-account').addEventListener('click', function() {
    fetch('/generate-line-login-url', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            person_id: '{{ $person->id }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.url) {
            window.location.href = data.url;
        } else {
            alert('LINE連携URLの生成に失敗しました。');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('エラーが発生しました。');
    });
});
</script>
</body>
</x-app-layout>