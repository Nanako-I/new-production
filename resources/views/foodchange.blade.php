<x-app-layout>

<!--ヘッダー[START]-->
<div class="flex items-center justify-center">
<!--<div style="display: flex; flex-direction: column;">-->
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
                <h2>{{$person->last_name}}{{$person->first_name}}さんの食事登録</h2>
                @php
                   $lastFood = $person->foods->last();
                @endphp
                @if(!is_null($lastFood))
                    （{{$lastFood->created_at->format('n/j G：i')}}に登録した内容）
                @endif
            </div>
        </div>
    </form>
    <form action="{{ route('food_update', ['people_id' => $person->id, 'id' => $lastFood->id]) }}" method="POST">
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
                        <div class="flex flex-col items-center">
                          <span class="text-gray-400 text-4xl" onclick="changeColorAndSize(this, 'rice_bowl_icon_1')">
                            <i class="fa-solid fa-bowl-rice text-red-300"  id="rice_bowl_icon_1" style="font-size: 1.5em; padding: 15px 5px; transition: transform 0.2s;"></i>
                          </span>
                        </div>
                    </div>
                        
                              
                          <div style="display: flex; flex-direction: column; align-items: center; margin: 10px 0;">
                                        <p class="text-gray-900 font-bold text-xl">昼食</p>
                                            <div style="display: flex; flex-direction: column; align-items: center; margin: 10px 0;">
                                                <select name="lunch" class="mx-1 my-1.5" style="width: 6rem;">
                                                    
                                                    <option value="登録なし" {{ is_null($lastFood->lunch) ? 'selected' : '' }}>選択</option>
                                                    <option value="あり" {{ $lastFood->lunch === 'あり' ? 'selected' : '' }}>あり</option>
                                                    <option value="なし" {{ $lastFood->lunch === 'なし' ? 'selected' : '' }}>なし</option>
                                                </select>
                                            </div>
                                      </div>
                              <div style="display: flex; flex-direction: column; align-items: center; margin: 10px 0;">
                                <p class="text-gray-900 font-bold text-xl">備考（メニューなど）<p>
                                <textarea id="result-speech" name="lunch_bikou" class="w-full max-w-lg" style="height: 150px;">{{ $lastFood->lunch_bikou }}</textarea>
                              </div>
                              
                              <div style="display: flex; flex-direction: column; align-items: center; margin: 10px 0;">
                                        <p class="text-gray-900 font-bold text-xl">間食</p>
                                            <div style="display: flex; flex-direction: column; align-items: center; margin: 10px 0;">
                                                <select name="oyatsu" class="mx-1 my-1.5" style="width: 6rem;">
                                                    <option value="登録なし" {{ is_null($lastFood->oyatsu) ? 'selected' : '' }}>選択</option>
                                                    <option value="あり" {{ $lastFood->oyatsu === 'あり' ? 'selected' : '' }}>あり</option>
                                                    <option value="なし" {{ $lastFood->oyatsu === 'なし' ? 'selected' : '' }}>なし</option>
                                                </select>
                                            </div>
                                      </div>
                                    <!--</div>-->
                                    <div style="display: flex; flex-direction: column; align-items: center; margin: 10px 0;">
                                      <p class="text-gray-900 font-bold text-xl">備考（メニューなど）<p>
                                      <textarea id="result-speech" name="oyatsu_bikou" class="w-full max-w-lg" style="height: 150px;">{{ $lastFood->oyatsu_bikou }}</textarea>
                                    </div>
                                 <button type="submit" class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-lg text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                      修正
                                </button>
             </div>
        </div>
    </div>
</form>

    <form action="{{ route('food.delete', [ 'id' => $food->id]) }}" method="POST">
    @csrf
        <div class="flex justify-center my-8">
            <button type="button" class="delete-btn font-semibold px-4 py-2 bg-gray-600 text-lg text-white rounded-md hover:bg-gray-500" data-id="{{ $food->id }}" data-toggle="modal" data-target="#confirmDeleteModal">
            このデータを削除する
            </button>
        </div>
    </form>

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

function oninput_rice(){
var rice_range = document.getElementById('rice_range');
const staple_food = document.getElementById("staple_food");
staple_food.value = rice_range.value;
};


function oninput_meal(){
var meal_range = document.getElementById('meal_range');
const side_dish = document.getElementById("side_dish");
side_dish.value = meal_range.value;
};

// スクロールイベント↓

function countScroll() {
var target = document.getElementById('target');
var x = target.scrollLeft;
document.getElementById('output').innerHTML = x;

// アイコンのサイズ変更
// var leftIcon = document.getElementById('leftIcon');
// var rightIcon = document.getElementById('rightIcon');
// var newSize = 2 + x / 100; // スクロール量に応じてサイズを変更する調整値
// leftIcon.style.fontSize = newSize + 'em';
// rightIcon.style.fontSize = newSize + 'em';

// アイコンの位置調整
// var iconWrapper = document.getElementById('iconWrapper');
// var maxScroll = target.scrollWidth - target.clientWidth;
// var iconPosition = x / maxScroll * (target.clientWidth - leftIcon.clientWidth);
// iconWrapper.style.left = iconPosition + 'px';
}

// スクロールイベントの監視
var target = document.getElementById('target');
target.addEventListener('scroll', countScroll);
</script>
</x-app-layout>