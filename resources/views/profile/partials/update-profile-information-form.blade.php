@vite(['resources/css/app.css', 'resources/js/app.js'])
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('プロフィール情報') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("アカウントのプロフィール情報を更新する。") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')
        
        <div>
            <x-input-label for="last_name" :value="__('姓')" />
            <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $user->last_name)" required autofocus autocomplete="last_name" />
            <x-input-error class="mt-2 text-red-500" :messages="$errors->get('last_name')" />
        </div>

        <div>
            <x-input-label for="first_name" :value="__('名')" />
            <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $user->first_name)" required autofocus autocomplete="first_name" />
            <x-input-error class="mt-2 text-red-500" :messages="$errors->get('first_name')" />
        </div>

        <div>
            <x-input-label for="last_name_kana" :value="__('セイ')" />
            <x-text-input id="last_name_kana" name="last_name_kana" type="text" class="mt-1 block w-full" :value="old('last_name_kana', $user->last_name_kana)" required autofocus autocomplete="last_name_kana" />
            <x-input-error class="mt-2 text-red-500" :messages="$errors->get('last_name_kana')" />
        </div>

        <div>
            <x-input-label for="first_name_kana" :value="__('メイ')" />
            <x-text-input id="first_name_kana" name="first_name_kana" type="text" class="mt-1 block w-full" :value="old('first_name_kana', $user->first_name_kana)" required autofocus autocomplete="first_name_kana" />
            <x-input-error class="mt-2 text-red-500" :messages="$errors->get('first_name_kana')" />
        </div>
        
    @hasanyrole('super administrator|facility staff administrator|client family user|client family reader')
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2 text-red-500" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
    @endhasanyrole

    <!-- @hasanyrole('super administrator|facility staff administrator|facility staff user|facility staff reader')
    <div>
        <x-input-label for="line_account" :value="__('LINEアカウント連携')" />
        @if($user->line_user_id)
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('LINEアカウントが連携されています。') }}
            </p>
        @else
            <a href="{{ route('line.login') }}" 
            target="_blank" 
            rel="noopener noreferrer" 
            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                {{ __('LINEアカウントを連携する') }}
            </a>
        @endif
    </div>
    @endhasanyrole -->

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('変更') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 20000)"
                    class="text-xl text-green-600 dark:text-gray-400"
                >{{ __('プロフィールが正常に変更されました。') }}</p>
            @endif
        </div>
    </form>
</section>