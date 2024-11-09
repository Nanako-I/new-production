<x-guest-layout>
    <!-- デバッグ表示を追加 -->
    @if(isset($userData))
        <div>
            <p>利用規約とプライバシーポリシーの同意状態:</p>
            <p>利用規約: {{ $userData['terms_accepted'] ? '同意済み' : '未同意' }}</p>
            <p>プライバシーポリシー: {{ $userData['privacy_accepted'] ? '同意済み' : '未同意' }}</p>
        </div>
    @endif

    <!-- 既存のフォーム -->
    <form action="{{ route('hogoshanumber.store') }}" method="POST">
        @csrf
        <!-- 既存のフォームフィールド -->
        @if(isset($userData))
            <input type="" name="terms_accepted_at" value="{{ $userData['terms_accepted_at'] ?? '' }}">
            <input type="" name="privacy_accepted_at" value="{{ $userData['privacy_accepted_at'] ?? '' }}">
        @endif
        @if (isset($error))
            <div style="color: red;">
                {!! $error !!}
            </div>
        @endif

        @if (isset($userData))
            <input type="" id="last_name" name="last_name" value="{{ $userData['last_name'] }}">
        
            <input type="" id="first_name" name="first_name" value="{{ $userData['first_name'] }}">
            <input type="" id="last_name_kana" name="last_name_kana" value="{{ $userData['last_name_kana'] }}">
            <input type="" id="first_name_kana" name="first_name_kana" value="{{ $userData['first_name_kana'] }}">
            <input type="" id="email" name="email" value="{{ $userData['email'] }}">
            <!-- パスワードは通常出力しないが、ここでは例として表示 -->
            <input type="" id="password" name="password" value="{{ $userData['password'] }}">
            <input type="" name="terms_accepted" value="{{ $userData['terms_accepted'] ? '1' : '0' }}">
            <input type="" name="privacy_accepted" value="{{ $userData['privacy_accepted'] ? '1' : '0' }}">
            <input type="" name="terms_accepted_at" value="{{ $userData['terms_accepted_at'] ?? '' }}">
            <input type="" name="privacy_accepted_at" value="{{ $userData['privacy_accepted_at'] ?? '' }}">
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
            
            <div class="flex flex-col items-center justify-center">
                <p class="text-gray-900 font-bold text-xl">ご家族の受給者証番号</p>
                <textarea id="jukyuusha_number" name="jukyuusha_number" class="w-3/4 max-w-lg font-bold text-xl" style="height: 50px;"></textarea>
            </div>
            
            <div class="flex flex-col items-center justify-center pt-2">
                <p class="text-gray-900 font-bold text-xl">ご家族の生年月日</p>
                <input name="date_of_birth" type="date" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-xl font-bold border-gray-300 rounded-md" placeholder="生年月日">
            </div>
            
            <div class="my-2" style="display: flex; justify-content: center; align-items: center; max-width: 300px;">
              <button type="submit" class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-lg text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                送信
              </button>
            </div>
        </div>
     </form>
</x-guest-layout>