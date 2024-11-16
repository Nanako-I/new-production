@vite(['resources/css/app.css', 'resources/js/app.js'])
<x-app-layout>
  @hasanyrole('super administrator|facility staff administrator|facility staff user|facility staff reader')
<div class="flex flex-row justify-start w-screen overflow-x-auto">
    <div class="slider">


        @csrf
        @if (isset($people) && !empty($people) && count($people) > 0)
        <div class="flex flex-row justify-center tw-flex-row h-150 -m-2">
            @foreach ($people as $person)
                <div class="p-2 h-full lg:w-1/3 md:w-full flex">
                    <div class="slide border-2 p-4 w-full md:w-64 lg:w-100 rounded-lg bg-white">
                        <a href="{{ url('people/'.$person->id.'/edit') }}" class="relative ml-2">
                            <div class="h-30 flex flex-row items-center rounded-lg bg-white">
                                @if ($person->filename)
                                    <img alt="team" class="w-16 h-16 bg-gray-100 object-cover object-center flex-shrink-0 rounded-full mr-4" src="{{ asset('storage/sample/person_photo/' . $person->filename) }}">
                                @else
                                    <img alt="team" class="w-16 h-16 bg-gray-100 object-cover object-center flex-shrink-0 rounded-full mr-4" src="https://dummyimage.com/80x80">
                                @endif
                                <div class="flex-grow">
                                    <h2 class="h2 text-gray-900 title-font font-bold text-2.5xl">{{ $person->last_name }} {{ $person->first_name }}</h2>
                                    <p class="text-gray-900 font-bold text-xs">{{ $person->date_of_birth }}生まれ</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        @else
            <p>登録された利用者がいません。</p>
        @endif
    </div>
</div>
@endhasanyrole
</x-app-layout>


                                            <!-- people.blade.phpにおいて、登録する送迎の要否（これをまるごとpeople.blade.phpにコピペしたら、チェックボックスは動作するが、利用者ごとの送迎登録はできない）↓ -->
                                            <div class="border-2 p-2 rounded-lg bg-white m-2">
                                            <div class="flex justify-start items-center">
                                                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
                                                <script src="https://kit.fontawesome.com/de653d534a.js" crossorigin="anonymous"></script>
                                                <i class="fa-solid fa-bus text-pink-600" style="font-size: 2em; padding: 0 5px; transition: transform 0.2s;"></i>
                                                <p class="font-bold text-xl ml-2">送迎</p>
                                            </div>

                                            <div class="flex flex-col justify-center items-center p-4">
                                                @php
                                                    // scheduled_visits リレーションをチェック
                                                    if ($person->scheduled_visits) {
                                                        $todayVisits = $person->scheduled_visits
                                                            ->where('arrival_datetime', '>=', now()->startOfDay())
                                                            ->where('arrival_datetime', '<=', now()->endOfDay());

                                                        $pickUpRequired = $todayVisits->where('pick_up', '必要')->isNotEmpty();
                                                        $dropOffRequired = $todayVisits->where('drop_off', '必要')->isNotEmpty();

                                                        // $todayVisits から最初の訪問データを取得
                                                        $scheduledVisit = $todayVisits->first();
                                                    } else {
                                                        $todayVisits = collect(); // 空のコレクションを定義
                                                        $pickUpRequired = false;
                                                        $dropOffRequired = false;
                                                        $scheduledVisit = null;
                                                    }
                                                @endphp

                                                @if ($scheduledVisit)
                                                    <div class="items-center mr-4">
                                                        <!-- 迎えボタン -->
                                                        <div class=" items-center mr-4">
                                                            <label class="text-red-500 font-bold text-lg">迎え:</label>
                                                            @if ($pickUpRequired)
                                                                <span class="text-red-500 font-bold">必要</span>
                                                                <button type="button" id="pickupButton" class="checkbox-button {{ $scheduledVisit->transport && $scheduledVisit->transport->pickup_completed ? 'checked' : '' }}"
                                                                    onclick="toggleCheck('pickup', {{ $scheduledVisit->id }})">
                                                                    {{ $scheduledVisit->transport && $scheduledVisit->transport->pickup_completed ? '✔' : '✔' }}
                                                                </button>
                                                            @else
                                                                <span class="text-gray-500 font-bold">不要</span>
                                                            @endif
                                                        </div>

                                                        <!-- 送りボタン -->
                                                        <div class=" items-center　mr-4">
                                                            <label class="text-red-500 font-bold text-lg">送り:</label>
                                                            @if ($dropOffRequired)
                                                                <span class="text-red-500 font-bold">必要</span>
                                                                <button type="button" id="dropoffButton" class="checkbox-button {{ $scheduledVisit->transport && $scheduledVisit->transport->dropoff_completed ? 'checked' : '' }}"
                                                                    onclick="toggleCheck('dropoff', {{ $scheduledVisit->id }})">
                                                                    {{ $scheduledVisit->transport && $scheduledVisit->transport->dropoff_completed ? '✔' : '✔' }}
                                                                </button>
                                                            @else
                                                                <span class="text-gray-500 font-bold">不要</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($todayVisits->isEmpty())
                                                <br><span class="text-red-500">本日の訪問予定はありません</span>
                                                @endif
                                            </div>


                                        </div>


                                        <!-- チェックボックス風ボタン -->
                                        <style>
                                            .checkbox-button {
                                                display: inline-block;
                                                width: 20px;
                                                height: 20px;
                                                color: white;
                                                background-color: #d7d4d4;
                                                border: 0px solid #a2a1a1bf;
                                                border-radius: 3px;
                                                text-align: center;
                                                cursor: pointer;
                                                font-size: 1rem;
                                                line-height: 0.5rem;
                                                margin-left: 3px;
                                                padding-bottom: 0px;
                                            }

                                            .checkbox-button.checked {
                                                background-color: #4caf50;
                                                color: white;
                                                border-color: #4caf50;
                                                line-height: 0.5rem;
                                                padding: 1px;
                                                border: 1.5px solid #4caf50;
                                                width: 20px;
                                                height: 20px;
                                                margin-left: 3px;  /* Adds space between the label and the button */


                                            }

                                            /* Adjust spacing for better readability */
                                            .flex.items-center {
                                                margin-bottom: 8px;
                                            }

                                            .text-red-500 {
                                                margin-right: 10px;  /* Adds more space between the text and the button */
                                            }

                                        </style>

                                        <!-- JavaScript -->
                                        <script>
                                            // AJAXリクエストを送信する関数
                                        function toggleCheck(type, scheduledVisitId) {
                                            const button = document.getElementById(type + 'Button');
                                            let completed = button.classList.contains('checked') ? 0 : 1;

                                            // チェック状態のトグル
                                            if (completed === 1) {
                                                button.classList.add('checked');
                                                button.innerHTML = '✔';
                                            } else {
                                                button.classList.remove('checked');
                                                button.innerHTML = '✔';
                                            }

                                            // AJAXリクエストを送信
                                            fetch(`/scheduledVisit/${scheduledVisitId}/updateTransport`, {
                                                method: 'PUT',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                },
                                                body: JSON.stringify({
                                                    [type + '_completed']: completed
                                                })
                                            }).then(response => {
                                                if (!response.ok) {
                                                    throw new Error('Failed to update transport status');
                                                }
                                                return response.json();
                                            }).then(data => {
                                                console.log('Transport status updated:', data);
                                            }).catch(error => {
                                                console.error('Error:', error);
                                            });
                                        }

                                        </script>