<x-guest-layout>
    <form action="{{ route('facilityregister.store') }}" method="POST" class="flex flex-col items-center justify-center space-y-4">
        @csrf
        
        <!-- ユーザー情報の隠しフィールド -->
        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
        @if ($errors->has('facility_key'))
                <div style="color: red;">
                    {{ $errors->first('facility_key') }}
                </div>
            @endif
        <!-- Facility ID -->
        <div class="flex flex-col items-center justify-center">
            <p class="text-gray-900 font-bold text-xl">事業所キー</p>
            <input type="text" id="facility_key" name="facility_key" class="w-full max-w-lg font-bold text-xl" maxlength="10" required>
            <p id="facility_key_hint" class="text-gray-600">10桁までのアルファベット・数字で登録してください</p>
            <p id="facility_key_error" class="text-red-600 hidden"></p>
        </div>

        <!-- Name -->
        <div class="flex flex-col items-center justify-center">
            <p class="text-gray-900 font-bold text-xl">事業所名</p>
            <textarea id="facility_name" name="facility_name" class="w-full max-w-lg font-bold text-xl" style="height: 100px;" required></textarea>
        </div>
        
        <!-- Submit Button -->
        <div class="flex justify-center">
            <button type="submit" class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-lg text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                送信
            </button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const facilityIdInput = document.getElementById('facility_key');
            const facilityIdError = document.getElementById('facility_key_error');

            facilityIdInput.addEventListener('input', function() {
                const value = facilityIdInput.value;
                const isValidLength = value.length <= 10;
                const isValidChars = /^[a-zA-Z0-9]*$/.test(value);

                if (!isValidLength) {
                    facilityIdError.textContent = '10桁までで登録してください';
                    facilityIdError.classList.remove('hidden');
                } else if (!isValidChars) {
                    facilityIdError.textContent = 'アルファベット・数字で登録してください';
                    facilityIdError.classList.remove('hidden');
                } else {
                    facilityIdError.classList.add('hidden');
                }
            });
        });
    </script>
</x-guest-layout>