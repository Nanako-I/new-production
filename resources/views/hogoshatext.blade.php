<x-app-layout>
    <div class="center-container">
        <div class="flex items-center justify-center my-2 font-bold text-2xl">
            <h2>事業所に伝えたいこと</h2>
        </div>

        <form action="{{ route('hogoshatext.show', $person->id) }}" method="GET" class="w-full max-w-lg mx-auto mt-4">
            @csrf
            <div class="flex flex-col items-center my-4">
                <label for="selected_date" class="text-gray-900 font-bold text-xl">日付選択：</label>
                <input type="date" name="selected_date" id="selected_date" value="{{ $selectedDate }}">
            </div>
        </form>

        <div id="hogoshatexts-container" class="mt-4 w-full max-w-lg mx-auto">
            @section('hogoshatexts')
                @if($hogoshatexts->isNotEmpty())
                    <h3 class="text-xl font-bold mb-2">{{ $selectedDate }}の記録:</h3>
                    @foreach($hogoshatexts as $hogosha_text)
                        <div class="bg-white p-4 rounded-lg shadow mb-4 w-full">
                            <p class="text-gray-700">{{ $hogosha_text->notebook }}</p>
                            <p class="text-sm text-gray-500 mt-2">記録時間: {{ $hogosha_text->created_at->format('H:i') }}</p>
                        </div>
                    @endforeach
                @else
                    <p class="text-center mt-4 w-full max-w-lg mx-auto">選択された日付の記録はありません。</p>
                @endif
            @show
        </div>

        <form action="{{ route('hogoshatext.store', $person->id) }}" method="POST" class="w-full max-w-lg mx-auto mt-4">
            @csrf
            <div class="flex flex-col items-center my-4 w-full">
                @error('notebook')
                    <p class="text-red-500 text-lg font-bold mt-2">{{ $message }}</p>
                @enderror
                <textarea name="notebook" class="w-full font-bold p-4 rounded-lg shadow" style="height: 300px;">{{ old('notebook') }}</textarea>
            </div>
            <div class="flex justify-center my-2">
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-lg text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    事業所に送信
                </button>
            </div>
        </form>
    </div>

    <style>
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background: linear-gradient(135deg, rgb(253, 219, 146,0), rgb(209, 253, 255,1));
        }
        h2 {
            font-family: Arial, sans-serif;
            font-size: 20px;
            text-decoration: underline;
        }
        .center-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 100vh;
            width: 100%;
            padding: 20px;
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('selected_date');
        const hogoshatextsContainer = document.getElementById('hogoshatexts-container');

        dateInput.addEventListener('change', function() {
            const selectedDate = this.value;
            const url = `{{ route('hogoshatext.show', $person->id) }}?selected_date=${selectedDate}`;

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                hogoshatextsContainer.innerHTML = data.html;
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
    </script>
</x-app-layout>