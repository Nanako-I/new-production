<x-guest-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-4">利用規約とプライバシーポリシー</h2>
                
                <!-- @if(isset($people_id))
                    <p class="mb-4 text-green-600">People ID: {{ $people_id }}</p>
                @else
                    <p class="mb-4 text-red-600">People IDが見つかりません。</p>
                @endif -->
                
                <form method="POST" action="{{ route('terms.agreement') }}">
                    @csrf
                    @if(isset($people_id))
                        <input type="hidden" name="people_id" value="{{ $people_id }}">
                    @endif
                    
                    <div class="mb-6">
                        <div class="border p-4 mb-4 h-48 overflow-y-auto">
                            <!-- 利用規約の内容 -->
                            <h3 class="font-bold mb-2">利用規約</h3>
                            <style>
                                .terms-text {
                                    white-space: pre-wrap; /* 改行と空白をそのまま表示 */
                                    margin: 20px;
                                    font-size: 14px;
                                    line-height: 1.6;
                                }
                            </style>
                            <div class="terms-text">
                                {{ $termsText }}
                            </div>
                        </div>
                        
                        <div class="border p-4 mb-4 h-48 overflow-y-auto">
                            <!-- プライバシーポリシーの内容 -->
                            <h3 class="font-bold mb-2">プライバシーポリシー</h3>
                            <style>
                                .privacypolicy-text {
                                    white-space: pre-wrap; /* 改行と空白をそのまま表示 */
                                    margin: 20px;
                                    font-size: 14px;
                                    line-height: 1.6;
                                }
                            </style>
                            <div class="privacypolicy-text">
                                {{ $privacypolicyText }}
                            </div>
                        </div>
                        
                        <div class="flex flex-col space-y-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="terms_accepted" class="form-checkbox" required>
                                <span class="ml-2">利用規約に同意します</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="privacy_accepted" class="form-checkbox" required>
                                <span class="ml-2">プライバシーポリシーに同意します</span>
                            </label>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 text-red-600">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="flex items-center justify-end">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            同意して続ける
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>

<style>
.terms-text {
    white-space: pre-wrap; /* 改行と空白をそのまま表示 */
}
</style>