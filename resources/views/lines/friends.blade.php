<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('友達を選択') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('line.store_friends') }}">
                        @csrf
                        @foreach ($friendIds as $friendId)
                            <div class="mb-4">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="form-checkbox" name="friends[]" value="{{ $friendId }}">
                                    <span class="ml-2">友達 ID: {{ $friendId }}</span>
                                </label>
                            </div>
                        @endforeach
                        <div class="mt-4">
                            <x-button type="submit">
                                {{ __('選択した友達を登録') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

