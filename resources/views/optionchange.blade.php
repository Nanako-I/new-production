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
                <h2>{{$person->last_name}}{{$person->first_name}}さんの{{ $optionTitle }}登録</h2>
            </div>
        </div>
    </form>
    <form id="option-form" action="{{ route('options.item.update', ['people_id' => $person->id, 'id' => $optionItem->id]) }}" method="POST">
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
                           
                                
                        </div>
                    
                    <div class="items-center justify-center p-4">
                        <input type="hidden" name="people_id" value="{{ $person->id }}">
                                                        
                                                        
                        <div class="flex flex-col items-center justify-center p-4">
                            <form action="{{ url('optionchange/' . $person->id . '/' . $optionItem->id) }}" method="POST">
                           
                                @csrf
                                @method('PATCH')
                                
                                <input type="hidden" name="people_id" value="{{ $person->id }}">
                                <input type="hidden" name="option_id" value="{{ $optionItem->option_id }}">
                               
                                @foreach($validItems as $itemKey => $item)
                                    <div class="flex items-center mb-4">
                                        <input type="checkbox" 
                                            id="{{ $itemKey }}" 
                                            name="{{ $itemKey }}" 
                                            value="1" 
                                            class="w-6 h-6 mr-2"
                                            @if($item['isChecked']) checked @endif>
                                        <label for="{{ $itemKey }}" class="text-xl font-bold">{{ $item['optionData'] }}</label>
                                    </div>
                                @endforeach

                                <textarea id="bikou" name="bikou" class="w-full max-w-lg" style="height: 200px;">{{ $optionItem->bikou }}</textarea>

                                <button type="submit" class="bg-gray-800 text-white px-6 py-3 font-bold rounded mt-4">更新</button>
                            </form>
                </div>
             </div>
        </div>
    </div>
</form>

    <form action="{{ route('option.delete', [ 'id' => $optionItem->id]) }}" method="POST">
    @csrf
        <div class="flex justify-center my-4">
            <button type="button" class="delete-btn font-semibold px-4 py-2 bg-gray-600 text-lg text-white rounded-md hover:bg-gray-500" data-id="{{ $optionItem->id  }}" data-toggle="modal" data-target="#confirmDeleteModal">
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

    document.getElementById('option-form').addEventListener('submit', function(event) {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        const textarea = document.getElementById('bikou');
        let isChecked = false;

        // チェックボックスが1つでもチェックされているか確認
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                isChecked = true;
            }
        });

        // チェックボックスがすべて未チェックかつ備考欄が空の場合
        if (!isChecked && textarea.value.trim() === '') {
            event.preventDefault(); // フォームの送信をキャンセル
            alert('チェックボックスもしくは備考欄に入力してください');
        }
    });
</script>

</x-app-layout>