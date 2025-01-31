@vite(['resources/css/app.css', 'resources/js/app.js'])
<x-app-layout>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<script src="https://kit.fontawesome.com/de653d534a.js" crossorigin="anonymous"></script>
        @if ($errors->any())
            <div style="color: red; font-weight: bold; background-color: #ffe6e6; border: 2px solid red; padding: 10px; border-radius: 5px;">
                <ul style="list-style-type: none; padding-left: 0;">
                    @foreach ($errors->all() as $error)
                        <li style="margin-bottom: 5px; color: red; font-weight: bold; font-size: 1.1em;">
                            <i class="fas fa-exclamation-triangle" style="margin-right: 5px;"></i>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-500 text-white p-4 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->has('fields'))
    <li style="margin-bottom: 5px; color: red; font-weight: bold; font-size: 1.1em;">
        <i class="fas fa-exclamation-triangle" style="margin-right: 5px;"></i>
        {{ $errors->first('fields') }}
    </li>
@endif

    @php
        $user = Auth::user();
        $permissions = $user->getPermissionsViaRoles();
    @endphp 

    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
        <script>
            window.peopleIds = @json($people->pluck('id'));
            window.UserId = {{ Auth::id() }};
        </script>
    <body>
<style>
  /* フォントを指定 */
  
  body {
    font-family: 'Noto Sans JP', sans-serif; /* フォントをArialに設定 */
  }

  .backcolor {
    font-family: 'Noto Sans JP', sans-serif; /* フォントをArialに設定 */
  background: linear-gradient(135deg, rgb(209, 253, 255,0.5), rgb(253, 219, 146,1));
  }
  
  </style>
        <!--// 処理-->
        
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">
        
           
   
    
 <!-- 利用者情報 -->

@hasanyrole('super administrator|facility staff administrator|facility staff user|facility staff reader')
  <div class="backcolor flex flex-row justify-start w-screen">
  <!-- <div class="flex flex-row justify-start w-screen overflow-x-auto"> -->
    <div class="slider">
    @csrf
    
<!-- 左半分: 利用者情報 -->

<div class="p-4 overflow-x-auto md:overflow-y-auto">
    @foreach ($people as $person)
    
        <div class="inline-block md:block bg-white rounded-lg shadow-md p-4 mb-4 cursor-pointer person-info" 
             data-person-id="{{ $person->id }}"
             data-last-name="{{ $person->last_name }}"
             data-first-name="{{ $person->first_name }}"
             data-date-of-birth="{{ $person->date_of_birth }}">
            <h2 class="text-gray-900 font-bold text-2xl mb-2">{{ $person->last_name }} {{ $person->first_name }}</h2>
            <p class="text-gray-700 mb-2">{{ $person->date_of_birth }} 生まれ</p>
            @php
                        $today = \Carbon\Carbon::now()->toDateString();
                        $todayHogoshaText = $person->hogosha_texts()
                            ->whereDate('created_at', $today)
                            ->latest()  // 最新のレコードを取得
                            ->first();
                    @endphp
                    
        <!-- @if ($person->unreadMessages)
    
            <a href="{{ url('hogoshatext/'.$person->id) }}" class="relative ml-2 flex items-center">
                    @csrf -->
                    <!-- 未読メッセージがある場合に new マークを表示 -->
                    <!-- <span id="new-indicator-{{ $person->id }}" class="ml-2 text-red-500 text-xl font-bold"><i class="fa-regular fa-envelope text-red-500" style="font-size: 1.5em; padding: 0 5px; transition: transform 0.2s;"></i>New</span>
                </a>
        @endif -->
     <div class="flex flex-col mb-2">
        @switch($person->messageStatus)
    @case('unread')
        <span id="new-indicator-{{ $person->id }}" class="text-red-500 text-xl font-bold">
            <i class="fa-regular fa-envelope text-red-500" style="font-size: 1.5em; padding: 0 5px; transition: transform 0.2s;"></i>
            メッセージあり
        </span>
        @break

    @case('today')
        <span id="new-indicator-{{ $person->id }}" class="text-yellow-500 text-xl font-bold">
            <i class="fa-regular fa-comment-dots text-yellow-500" style="font-size: 1.5em; padding: 0 5px; transition: transform 0.2s;"></i>
            本日連絡あり
        </span>
        @break

    @case('older')
        <span class="text-gray-500 text-xl font-bold">
            過去の連絡あり
        </span>
        @break

    @default
        <span class="text-gray-500 text-xl font-bold">
            未読なし
        </span>
@endswitch
</div>


<div class="flex flex-col mb-2">
    @if (isset($isConfirmed[$person->id]))
        <!-- 確定済みの場合の表示 -->
        @csrf
            <span id="new-indicator-{{ $person->id }}" class="text-orange-400 text-xl font-bold">
                本日の連絡帳：済
            </span>
    @else
        <!-- 未確定の場合の表示 -->
        <span id="new-indicator-{{ $person->id }}" class="text-gray-500 text-xl font-bold">
            本日の連絡帳：未
        </span>
    @endif
</div>
     </div>                              
    @endforeach
    
                                    
</div>
<!-- 右半分: 利用者詳細情報 -->
<div id="person-details" class="p-4 flex flex-col overflow-y-auto">
@if(isset($selectedPerson))
     @include('partials._people_content', ['person' => $selectedPerson,'isConfirmed' => $isConfirmed])
        @else
            <p>利用者を選択してください。</p>
        @endif
<!-- ここに部分ビューの内容が表示されます -->
</div>
        @endhasanyrole
        </div>   
        
        <style>
                        .slider {
                            display: flex;
                            flex-wrap: wrap;
                            justify-content: left; /* 中央揃え */
                            gap: 10px; /* スライド間の余白 */
                            overflow-x: scroll; /* 水平方向にスクロール可能 */
                            white-space: nowrap; /* 子要素を横並びにする */
                            width: 100%;
                        }

                        .slider > div{
                            width: 100%;
                        }

                        @media screen and (min-width: 768px){
                            .slider > div{
                                width: 49%;
                                max-height: 80vh;
                            }
                        }

                        
                        .slide {
                        background: rgb(244,244,244);                       
                      }
                </style>


<!--</section>-->
</div>
 
 <!--全エリア[END]-->

</body>
</html>
<script>
// document.addEventListener('DOMContentLoaded', function() {
//     const personElements = document.querySelectorAll('.person-info');
//     const recordLists = document.querySelectorAll('[id^="record-list-"]');

//     personElements.forEach(element => {
//         element.addEventListener('click', function() {
//             const personId = this.getAttribute('data-person-id');
            
//             // Hide all record lists
//             recordLists.forEach(list => {
//                 list.classList.add('hidden');
//             });

//             // Show the selected person's record list
//             const selectedRecordList = document.getElementById(`record-list-${personId}`);
//             if (selectedRecordList) {
//                 selectedRecordList.classList.remove('hidden');
//             }
//         });
//     });
// });

document.addEventListener('DOMContentLoaded', function() {
    const personElements = document.querySelectorAll('.person-info');

    personElements.forEach(personElement => {
        personElement.addEventListener('click', function() {
            const personId = this.getAttribute('data-person-id');
            console.log(personId);
            console.log(personElement);
            fetch(`/people/${personId}/content`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('person-details').innerHTML = html;
                })
                .catch(error => console.error('Error fetching person details:', error));
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const personDetailsContainer = document.getElementById('person-details');

    // イベントデリゲーションを使用して、動的に追加されたラジオボタンにイベントを設定
    personDetailsContainer.addEventListener('change', function(event) {
        if (event.target.matches('input[name="meal_type"]')) {
            const lunchSection = document.getElementById('lunch-section');
            const oyatsuSection = document.getElementById('oyatsu-section');
            const lunchSelect = document.querySelector('select[name="lunch"]');
            const oyatsuSelect = document.querySelector('select[name="oyatsu"]');
            const submitButton = document.getElementById('submit-button');

            // ラジオボタンが選択されたら送信ボタンを表示
            submitButton.classList.remove('hidden');
            if (event.target.value === 'lunch_only') {
                lunchSection.style.display = 'block';
                oyatsuSection.style.display = 'none';

                // lunch のデフォルト値を 'あり' に、oyatsu のデフォルト値を 'なし' に
                lunchSelect.value = 'あり';
                oyatsuSelect.value = 'なし';

            } else if (event.target.value === 'lunch_and_oyatsu') {
                lunchSection.style.display = 'block';
                oyatsuSection.style.display = 'block';

                // 両方のデフォルト値を 'あり' に
                lunchSelect.value = 'あり';
                oyatsuSelect.value = 'あり';
            } else if (event.target.value === 'oyatsu_only') {
                lunchSection.style.display = 'none';
                oyatsuSection.style.display = 'block';

                // 昼食のデフォルト値を 'なし' に
                lunchSelect.value = 'なし';
                oyatsuSelect.value = 'あり';
            }
        }
    });
    
    // イベントデリゲーションを使用して、動的に追加されたフォームにイベントを設定
    // personDetailsContainer.addEventListener('submit', function(event) {
    //     const form = event.target;
    //     const actionPattern = new RegExp('^' + form.action.replace(/:\w+/g, '\\d+'));
        
    //     if (actionPattern.test(form.action)) {
    //         const checkboxes = form.querySelectorAll('.option-checkbox');
    //         const bikou = form.querySelector('.option-bikou');
    //         const hasInputField = form.querySelector('input[name="has_input"]');

    //         updateHasInput();
    //         if (hasInputField.value === '0') {
    //             event.preventDefault();
    //             alert('チェックボックス、もしくは備考欄に入力してください。');
    //         }

    //         checkboxes.forEach(checkbox => {
    //             checkbox.addEventListener('change', updateHasInput);
    //         });

    //         bikou.addEventListener('input', updateHasInput);

    //         function updateHasInput() {
    //             let hasChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
    //             let hasBikou = bikou.value.trim() !== '';
    //             hasInputField.value = (hasChecked || hasBikou) ? '1' : '0';
    //         }
    //     }
    // });
});

</script>
</x-app-layout>