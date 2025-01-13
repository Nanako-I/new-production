<x-guest-layout>
    <!-- デバッグ表示を追加 -->
    <!-- @if(isset($userData))
        <div>
            <p>利用規約とプライバシーポリシーの同意状態:</p>
            <p>利用規約: {{ $userData['terms_accepted'] ? '同意済み' : '未同意' }}</p>
            <p>プライバシーポリシー: {{ $userData['privacy_accepted'] ? '同意済み' : '未同意' }}</p>
        </div>
    @endif -->

    <!-- 既存のフォーム -->
    <form action="{{ route('hogoshanumber.store') }}" method="POST">
        @csrf
        <!-- 既存のフォームフィールド -->
        @if(isset($userData))
            <input type="hidden" name="terms_accepted_at" value="{{ $userData['terms_accepted_at'] ?? '' }}">
            <input type="hidden" name="privacy_accepted_at" value="{{ $userData['privacy_accepted_at'] ?? '' }}">
        @endif
        @if (isset($error))
            <div style="color: red;">
                {!! $error !!}
            </div>
        @endif
        <!-- <p>
    @if (isset($personId))
        Person ID: {{ $personId }}<br>
        @if (isset($dateOfBirth))
            Date of Birth: {{ $dateOfBirth }}
        @else
            Date of Birth は見つかりませんでした。
        @endif
    @else
        Person ID は設定されていません。
    @endif
</p> -->

        @if (isset($userData))
            <input type="hidden" id="last_name" name="last_name" value="{{ $userData['last_name'] }}">
        
            <input type="hidden" id="first_name" name="first_name" value="{{ $userData['first_name'] }}">
            <input type="hidden" id="last_name_kana" name="last_name_kana" value="{{ $userData['last_name_kana'] }}">
            <input type="hidden" id="first_name_kana" name="first_name_kana" value="{{ $userData['first_name_kana'] }}">
            <input type="hidden" id="email" name="email" value="{{ $userData['email'] }}">
            <!-- パスワードは通常出力しないが、ここでは例として表示 -->
            <input type="hidden" id="password" name="password" value="{{ $userData['password'] }}">
            <input type="hidden" name="terms_accepted" value="{{ $userData['terms_accepted'] ? '1' : '0' }}">
            <input type="hidden" name="privacy_accepted" value="{{ $userData['privacy_accepted'] ? '1' : '0' }}">
            <input type="hidden" name="terms_accepted_at" value="{{ $userData['terms_accepted_at'] ?? '' }}">
            <input type="hidden" name="privacy_accepted_at" value="{{ $userData['privacy_accepted_at'] ?? '' }}">
        @endif
        <div class="flex items-center justify-center">

       
        </div>
         
     <!-- Name -->
        <div class="flex flex-col items-center justify-center">
            <!-- Validation Errors -->
            @if ($errors->any())
                <div style="color: red;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <!-- <div class="flex flex-col items-center justify-center">
                <p class="text-gray-900 font-bold text-xl mb-2">ご家族の受給者証番号（10桁）</p>
                <div class="border-2 border-gray-300 rounded-md p-2 mb-4">
                    <input type="text" id="jukyuusha_number" name="jukyuusha_number" 
                        class="w-full text-center text-2xl font-bold tracking-widest" 
                        maxlength="10" placeholder="0000000000" 
                        style="letter-spacing: 0.5em;">
                </div>
                <div id="number-error" class="text-red-500 text-sm hidden"></div>
            </div> -->
            
            <div class="flex flex-col items-center justify-center pt-2">
                <p class="text-gray-900 font-bold text-xl">お子さまの生年月日をご入力ください</p>
                <input name="date_of_birth" type="date" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-xl font-bold border-gray-300 rounded-md" placeholder="生年月日">
            </div>
            
            <div class="my-2" style="display: flex; justify-content: center; align-items: center; max-width: 300px;">
              <button type="submit" class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-lg text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                送信
              </button>
            </div>
        </div>
     </form>

    <!-- <script>
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

    </script> -->
</x-guest-layout>