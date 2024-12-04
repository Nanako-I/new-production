<x-guest-layout>
    <form method="POST" action="{{ route('facility.key.submit') }}" class="flex flex-col items-center justify-center space-y-4">
        @csrf

        <!-- Facility Key -->
        <div>
            <x-input-label for="facility_key" :value="__('施設キー')" class="text-2xl" />
            <x-text-input id="facility_key" class="block mt-1 w-full text-xl" type="text" name="facility_key" required autofocus />
            <x-input-error :messages="$errors->get('facility_key')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <div class="flex justify-center mt-4">
            <div class="text-gray-700 text-center px-4 py-2 m-2">
              <button type="submit" class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-lg text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                送信
              </button>
            </div>
        </div>
    </form>
</x-guest-layout>