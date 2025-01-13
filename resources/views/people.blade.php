@vite(['resources/css/app.css', 'resources/js/app.js'])
<x-app-layout>
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
        
           
   <!-- <div class="flex flex-col items-center justify-center w-full my-2">
        <style>
         @import url('https://fonts.googleapis.com/css2?family=Arial&display=swap');
            h1 {
            font-family: Arial, sans-serif; /* フォントをArialに設定 */
          }
        </style>
      <h1 class="sm:text-2xl text-3xl font-bold title-font mb-4 text-gray-900" _msttexthash="91611" _msthidden="1" _msthash="63"></h1>
    </div> -->
    
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
                                            
                                            @if ($person->unreadMessages)
                                    @if ($todayHogoshaText)
                                        <!-- 登録済みの場合 -->
                                        <a href="{{ url('hogoshatext/'.$person->id) }}" class="relative ml-2 flex items-center">
                                            @csrf
                                            <!-- 未読メッセージがある場合に new マークを表示 -->
                                            <span id="new-indicator-{{ $person->id }}" class="ml-2 text-red-500 text-xl font-bold">保護者からメッセージあり</span>
                                        </a>
                                    @else
                                        <!-- 未登録の場合 -->
                                        <a href="{{ url('hogoshatext/'.$person->id) }}" class="relative">
                                            <summary class="text-red-500 font-bold text-xl">登録する</summary>
                                            @csrf
                                        </a>
                                    @endif
                                @endif
                                        </div>
                                    @endforeach
                                </div>
           
       
        @endhasanyrole
        </div>   
    
<!--</section>-->
</div>
 
 <!--全エリア[END]-->

</body>
</html>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const personElements = document.querySelectorAll('.person-info');
    const recordLists = document.querySelectorAll('[id^="record-list-"]');

    personElements.forEach(element => {
        element.addEventListener('click', function() {
            const personId = this.getAttribute('data-person-id');
            
            // Hide all record lists
            recordLists.forEach(list => {
                list.classList.add('hidden');
            });

            // Show the selected person's record list
            const selectedRecordList = document.getElementById(`record-list-${personId}`);
            if (selectedRecordList) {
                selectedRecordList.classList.remove('hidden');
            }

            // Optionally, you can add AJAX call here to fetch and update records
            // fetchRecords(personId);
        });
    });
});
</script>
</x-app-layout>