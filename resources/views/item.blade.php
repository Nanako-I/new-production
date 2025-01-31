<x-app-layout>
    <div class="flex items-center justify-center">
        <div class="flex flex-col items-center">
            <!-- フラッシュメッセージの表示（オプション） -->
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            <!-- エラーメッセージの表示（APIリクエストエラー用） -->
            <div id="form-error-message" class="error-message hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">エラーが発生しました。再度お試しください。</span>
            </div>

                @csrf
                @method('PATCH')
                <style>
                    h2 {
                        font-family: Arial, sans-serif;
                        font-size: 20px;
                    }

                    p {
                        font-family: Arial, sans-serif;
                        font-size: 25px;
                        font-weight: bold;
                    }

                    .error-message {
                        color: red;
                        font-size: 14px;
                        margin-top: 4px;
                    }
                </style>
                <div class="flex items-center justify-center" style="padding: 20px 0;">
                    <div class="flex flex-col items-center">
                        <h2>事業所の記録項目</h2>
                    </div>
                </div>

                <form action="{{ route('update.facility.items', $facility->id) }}" method="POST">
    @csrf
    @method('PATCH')
    @if(!empty($additionalItems))
    @foreach ($additionalItems as $item)
    <div class="flex items-center mb-2">
        <input type="checkbox" 
            name="selected_items[]" 
            value="{{ $item['id'] }}" 
            data-group-id="{{ $item['group_id'] }}"
            class="form-checkbox h-5 w-5 text-gray-600"
            {{ $item['flag'] == 1 ? 'checked' : '' }}>
        <label class="ml-2 text-gray-700">
            <p class="text-gray-900 font-bold text-xl px-1.5">{{ $item['title'] }}</p>
            @if(!empty($item['items']))
                <p class="text-gray-500 text-base px-1.5">({{ $item['items'] }})</p>
            @endif
        </label>
    </div>
    @endforeach
@endif

                <!-- 記録項目を追加するボタン -->
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
                <script src="https://kit.fontawesome.com/de653d534a.js" crossorigin="anonymous"></script>
                <div class="flex items-center justify-center mt-4 text-gray-900 font-bold text-xl cursor-pointer" id="add-item-button">
                <i class="fa-solid fa-plus"></i>
                        記録項目を追加
                </div>

           
                <!-- 更新ボタン -->
                <div class="flex items-center justify-center mt-4">
                    <button type="submit" id="update-button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    更新
                </button>
                </div>
            </form>
        </div>
    </div>
 <!-- モーダル -->
 <form action="{{ route('addItem.store', ['facility' => $facility->id, 'id' => $id]) }}" method="POST" id="add-item-form" class="w-full max-w-lg">
        @csrf
    <input type="hidden" name="facility_id" value="{{ $facility->id }}">
    <div id="add-item-modal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
        <div class="bg-white rounded-lg p-6 w-1/2">
            <h3 class="text-lg font-semibold mb-4">新しい記録項目を追加</h3>

            <!-- エラーメッセージの表示 -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            <!-- タイトル入力フィールド -->
            <input type="hidden" name="facility_id" value="{{ $facility->id }}">
            <label for="new-item-title" class="block text-gray-700 text-base font-bold mb-2">タイトル (例：宿題など)</label>
            <input type="text" id="new-item-title" name="title" class="border border-gray-300 rounded-md w-full px-3 py-2 mb-4" placeholder="タイトルを入力" maxlength="32">

          

            <!-- 項目入力フィールドのコンテナ -->
            <div id="item-fields-container">
                <label class="block text-gray-700 text-base font-bold mb-2">記録項目 (例：漢字ドリル、計算ドリルなど)</label>
               

                <!-- 項目入力フィールド（最初の一つ） -->
                @foreach(old('item', []) as $i => $value)
                    <div class="item-field mb-2 flex items-center">
                        <input type="text" name="item[]" class="border border-gray-300 rounded-md w-full px-3 py-2" value="{{ $value }}" placeholder="項目を入力" maxlength="32">
                        <button type="button" class="remove-item-field ml-2 text-red-500 hover:text-red-700">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        @error("item.$i")
                            <p class="text-red-500 text-base mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach

                <!-- 最初の1つの入力フィールドを表示 -->
                @if(count(old('item', [])) === 0)
                    <div class="item-field mb-2 flex items-center">
                        <input type="text" name="item[]" class="border border-gray-300 rounded-md w-full px-3 py-2" placeholder="項目を入力" maxlength="32">
                        <button type="button" class="remove-item-field ml-2 text-red-500 hover:text-red-700">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                @endif
            </div>

            <!-- 「＋」ボタン -->
            <button type="button" id="add-item-field-button" class="mb-4 text-blue-500 hover:text-blue-700">＋ 項目を追加</button>

            <!-- エラーメッセージ -->
            <div id="error-message" class="error-message hidden">32文字以上は入力できません。</div>

            <div class="flex justify-end mt-4">
                <button type="button" id="cancel-add-item" class="inline-flex items-center px-4 py-2 bg-white text-gray-800 border border-gray-800 rounded-md font-semibold text-base uppercase tracking-widest hover:bg-gray-100 active:bg-gray-200 focus:outline-none focus:border-gray-800 focus:ring ring-gray-200 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                    キャンセル
                </button>
                <button type="submit" id="confirm-add-item" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white border border-transparent rounded-md font-semibold text-base uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    追加
                </button>
            </div>
        </div>
    </div>
    </form>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('add-item-modal');
    const openModalButton = document.getElementById('add-item-button');
    const closeModalButton = document.getElementById('cancel-add-item');
    const addItemFieldButton = document.getElementById('add-item-field-button');
    const itemFieldsContainer = document.getElementById('item-fields-container');
    const addItemForm = document.getElementById('add-item-form');
    const errorMessageContainer = document.getElementById('error-message');

    openModalButton.addEventListener('click', function() {
        modal.classList.remove('hidden');
    });

    closeModalButton.addEventListener('click', function() {
        modal.classList.add('hidden');
        resetForm();
    });

    const maxItems = 5; // 最大項目数

    addItemFieldButton.addEventListener('click', function() {
        const currentItemFields = itemFieldsContainer.querySelectorAll('.item-field').length;
        if (currentItemFields < maxItems) {
            const itemFieldHTML = `
                <div class="item-field mb-2 flex items-center">
                    <input type="text" name="item[]" class="border border-gray-300 rounded-md w-full px-3 py-2" placeholder="項目を入力" maxlength="32">
                    <button type="button" class="remove-item-field ml-2 text-red-500 hover:text-red-700">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            `;
            itemFieldsContainer.insertAdjacentHTML('beforeend', itemFieldHTML);
        } else {
            alert('記録項目は最大5つまでです。');
        }
    });

    // ゴミ箱アイコンをクリックしたときにフィールドを削除
    itemFieldsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item-field')) {
            const itemField = e.target.closest('.item-field');
            itemField.remove();
        }
    });

    addItemForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitButton = document.getElementById('confirm-add-item');
        submitButton.disabled = true;
        errorMessageContainer.classList.add('hidden');

        fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('Response:', response);
            console.log('Response headers:', response.headers);
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            } else {
                throw new Error("サーバーからHTMLレスポンスが返されました。サーバーサイドでエラーが発生している可能性があります。");
            }
        })
        .then(data => {
            if (data.success) {
                modal.classList.add('hidden');
                resetForm();
                alert(data.message);
                location.reload();
            } else {
                throw new Error(data.message || 'エラーが発生しました');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            let errorMessage = 'エラーが発生しました: ';
            if (error.errors) {
                const itemErrors = Object.keys(error.errors).filter(key => key.startsWith('item.'));
                if (itemErrors.length > 0) {
                    errorMessage += '記録項目を入れてください。';
                } else {
                    errorMessage += Object.values(error.errors).flat().map(err => {
                        if (err.includes('title')) {
                            return 'タイトルを入れてください。';
                        }
                        return err;
                    }).join('\n');
                }
            } else if (error.message) {
                errorMessage += error.message;
            } else {
                errorMessage += '不明なエラー';
            }
            alert(errorMessage);
            console.log('Full error object:', error);
        })
        .finally(() => {
            submitButton.disabled = false;
        });
    });

    function resetForm() {
        addItemForm.reset();
        const itemFields = itemFieldsContainer.querySelectorAll('.item-field');
        for (let i = 1; i < itemFields.length; i++) {
            itemFields[i].remove();
        }
        errorMessageContainer.classList.add('hidden');
    }
});
    </script>
</x-app-layout>