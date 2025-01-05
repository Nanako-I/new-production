<x-app-layout>

<!--ヘッダー[START]-->
<div class="flex items-center justify-center">
<div class="flex flex-col items-center">
    <form action="{{ url('people' ) }}" method="POST" class="w-full max-w-lg">
                        @method('PATCH')
                        @csrf
        <style>
            h2 {
              font-family: Arial, sans-serif; /* フォントをArialに設定 */
              font-size: 20px; /* フォントサイズを20ピクセルに設定 */
            }
        </style>
        <div class ="flex items-center justify-center"  style="padding: 20px 0;">
            <div class="flex flex-col items-center">
                <h2>{{$person->last_name}}{{$person->first_name}}さんの利用時間</h2>
                @php
            
                 $lastTime = $person->times->last();
                @endphp
                @if(!is_null($lastTime))
                    （{{$lastTime->created_at->format('n/j G：i')}}に登録した内容）
                @endif
            </div>
        </div>
    </form>
      <form action="{{ url('timechange/'.$person->id. '/'.$lastTime->id) }}" method="POST">
        @csrf
         
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
        <script src="https://kit.fontawesome.com/de653d534a.js" crossorigin="anonymous"></script>
            <div class="flex flex-col items-center">
                <style>
                  p {
                    font-family: Arial, sans-serif; /* フォントをArialに設定 */
                    font-size: 25px; /* フォントサイズを20ピクセルに設定 */
                    font-weight: bold;
                  }
                </style>
                
                    <div class="flex items-center justify-center">
                         <input type="hidden" name="people_id" value="{{ $person->id }}">
                    </div>
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <div class="flex items-center justify-center ml-4">
                            @if (!is_null($lastTime))
                        </div>
                    
                    @php
                        $pick_upData = json_decode($lastTime->pick_up);
                        $sendData = json_decode($lastTime->send);
                        
                    @endphp
                    <div class="items-center justify-center p-4">
                        <input type="hidden" name="people_id" value="{{ $person->id }}">
                        
                        
                        @if (!is_null($lastTime->start_time) && !is_null($lastTime->end_time))
                        <div style="display: flex; flex-direction: row; align-items: center; margin-top: 0.5rem; margin-bottom: 0.5rem;" class="my-3">                                             
                            <p class="text-gray-900 font-bold text-xl px-1.5">利用時間　合計時間({{ $totalUsageTime }})</p>
                        </div>
                        @endif
                         <div style="display: flex; flex-direction: row; align-items: center; margin-top: 0.5rem; margin-bottom: 0.5rem;" class="my-3">
                            
                           <input type="date" name="date" id="usage_date" value="{{ \Carbon\Carbon::parse($lastTime->date)->format('Y-m-d') }}" required>
                        </div>

                        <div style="display: flex; flex-direction: row; align-items: center; margin-top: 0.5rem; margin-bottom: 0.5rem;" class="my-3">
                        <input type="time" name="start_time" id="scheduled-time" value="{{ $lastTime->start_time ? \Carbon\Carbon::parse($lastTime->start_time)->format('H:i') : '' }}">
                            <p class="text-gray-900 font-bold text-xl px-1.5">～</p>
                        </div>
                        
                        <div style="display: flex; flex-direction: row; align-items: center; margin-top: 0.5rem; margin-bottom: 0.5rem;" class="my-3">
                        <input type="time" name="end_time" id="scheduled-time" value="{{ $lastTime->end_time ? \Carbon\Carbon::parse($lastTime->end_time)->format('H:i') : '' }}">

                        </div>
                        
                        <div style="display: flex; flex-direction: row; align-items: center; margin-top: 0.5rem; margin-bottom: 0.5rem;" class="my-3">
                            <i class="fa-solid fa-school text-gray-700" style="font-size: 1.5em; transition: transform 0.2s;"></i>
                            <p class="text-gray-900 font-bold text-xl px-1.5">利用形態</p>
                        </div>
                        
                            <div style="display: flex; flex-direction: row; align-items: center; margin-top: 0.5rem; margin-bottom: 0.5rem;" class="my-3">
                                <select name="school" class="mx-1 my-1.5" style="width: 8rem;">
                                    
                                    <option value="登録なし"{{ $lastTime->school === '登録なし' ? ' selected' : '' }}>登録なし</option>
                                    <option value="授業終了後"{{ $lastTime->school === '授業終了後' ? ' selected' : '' }}>授業終了後</option>
                                    <option value="休校"{{ $lastTime->school === '休校' ? ' selected' : '' }}>休校</option>
                                </select>
                            </div>

                                <div style="display: flex; align-items: center; margin: 10px 0;">
                                <p class="text-gray-900 font-bold text-xl mb-2">サービス提供の状況</p>
                                </div>
                                <div class="flex items-center">
        <input type="checkbox" name="is_absent" value="1" {{ $lastTime->is_absent ? 'checked' : '' }}>
        <p class="text-gray-900 font-bold text-xl px-1.5">欠席</p>
    </div>
     <!-- 送迎状況 -->
    <div style="display: flex; flex-direction: column; align-items: flex-start; margin: 10px 0;">
        <p class="text-gray-900 font-bold text-xl mb-2">送迎状況</p>
        <div class="flex items-center">
            <input type="checkbox" name="pick_up" value="1" {{ $lastTime->pick_up ? 'checked' : '' }}>
            <p class="text-gray-900 font-bold text-xl px-1.5">迎え完了</p>
        </div>
        <div class="flex items-center mt-2">
            <input type="checkbox" name="send" value="1" {{ $lastTime->send ? 'checked' : '' }}>
            <p class="text-gray-900 font-bold text-xl px-1.5">送り完了</p>
        </div>
    </div>
                            
      
                            <!-- <div style="display: flex; flex-direction: row; align-items: center; margin-top: 0.5rem; margin-bottom: 0.5rem;" class="my-3">
                                <input type="checkbox" name="pick_up[]" value="迎え" @if(!empty($pick_upData)) checked @endif class="w-6 h-6">
                                <p class="text-gray-900 font-bold text-xl px-1.5">迎え</p>
                            </div>
                            
                            <div style="display: flex; flex-direction: row; align-items: center; margin-top: 0.5rem; margin-bottom: 0.5rem;" class="my-3">
                                <input type="checkbox" name="send[]" value="送り" @if(!empty($sendData)) checked @endif class="w-6 h-6">
                                <p class="text-gray-900 font-bold text-xl px-1.5">送り</p>
                            </div> -->
                    @endif
                    <div style="display: flex; flex-direction: column; align-items: center; margin: 10px 0;">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-lg text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                          修正
                    </button>
                    </div>
                </div>
                </div>
             </div>
            </form>

            <form action="{{ route('time.delete', [ 'id' => $lastTime->id]) }}" method="POST">
            @csrf
                <div class="flex justify-center my-4">
                    <button type="button" class="delete-btn font-semibold px-4 py-2 bg-gray-600 text-lg text-white rounded-md hover:bg-gray-500" data-id="{{ $lastTime->id }}" data-toggle="modal" data-target="#confirmDeleteModal">
                    このデータを削除する
                    </button>
                </div>
            </form>
        </div>
    </div>

<!-- モーダルダイアログ -->
<div class="modal fixed w-full h-full top-0 left-0 flex items-center justify-center hidden" id="confirmDeleteModal">
    <div class="modal-overlay absolute w-full h-full bg-gray-600 opacity-50"></div>

    <div class="modal-container bg-white w-full max-w-xs mx-auto rounded shadow-lg z-50 overflow-y-auto">
        <div class="modal-content py-4 text-left px-6">
            <!--Title-->
            <div class="flex justify-between items-center pb-3">
                <p class="text-2xl font-semibold">本当に削除しますか？</p>
                <div class="modal-close cursor-pointer z-50" data-dismiss="modal">
                <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                    <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                </svg>
                </div>
            </div>

            <!--Body-->
            <p class="text-base font-normal">削除したデータは復元できません。</p>
            <!--Footer-->
            <div class="flex justify-end pt-2">
                <button type="button" class="px-4 bg-blue-800 p-3 rounded-lg text-white hover:bg-blue-400 mr-2" data-dismiss="modal">キャンセル</button>
                <button type="button" class="px-4 bg-red-500 p-3 rounded-lg text-white hover:bg-red-400" id="deleteBtn">削除</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    var deleteForm; // 削除するフォームを保存する変数

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            deleteForm = this.closest('form'); // フォームを取得して保存
            document.getElementById('confirmDeleteModal').classList.remove('hidden');
        });
    });

    document.getElementById('deleteBtn').addEventListener('click', function() {
        deleteForm.submit(); // モーダルの削除ボタンがクリックされたらフォームを送信
        document.getElementById('confirmDeleteModal').classList.add('hidden');
    });

    document.querySelectorAll('[data-dismiss="modal"]').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('confirmDeleteModal').classList.add('hidden');
        });
    });
});
</script>
</x-app-layout>