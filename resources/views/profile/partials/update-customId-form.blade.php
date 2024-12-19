@vite(['resources/css/app.css', 'resources/js/app.js'])
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('IDを変更する') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('アルファベットもしくは数字') }}
        </p>
    </header>

    <form method="post" action="{{ route('custom.id.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <!-- <div>
            <x-input-label for="current_password" :value="__('現在のパスワード')" />
            <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" :value="old('password', $user->password)" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div> -->

        <div>
            <x-input-label for="custom_id" :value="__('新しいID')" />
            <x-text-input id="custom_id" name="custom_id" type="text" class="mt-1 block w-full" autocomplete="new-custom_id" />
            <x-input-error :messages="$errors->get('custom_id')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="custom_id_confirmation" :value="__('新しいID（確認用）')" />
            <x-text-input id="custom_id_confirmation" name="custom_id_confirmation" type="text" class="mt-1 block w-full" autocomplete="new-custom_id" />
            <x-input-error :messages="$errors->get('custom_id_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('変更') }}</x-primary-button>

            @if (session('status') === 'custom_id-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-xl text-green-600 dark:text-gray-400"
                >{{ __('IDが正常に変更されました。') }}</p>
            @endif
        </div>
    </form>
</section>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            @if ($errors->has('custom_id'))
                alert('{{ $errors->first('custom_id') }}');
            @endif
        });
    </script>