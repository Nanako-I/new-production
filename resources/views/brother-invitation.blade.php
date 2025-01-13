@vite(['resources/css/app.css', 'resources/js/app.js'])
<x-app-layout>
        @if(session('success'))
            <div class="bg-green-500 text-white p-4 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight" style="width: 100%;">
            {{ __('ご家族と利用者リスト') }}
        </h2>
    </x-slot>

    <body class="h-full w-full">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
        <script src="https://kit.fontawesome.com/de653d534a.js" crossorigin="anonymous"></script>

        <style>
            /* li要素のテキストカラーをグレーに設定 */
            #family-list li {
                color: gray;
            }

            /* 利用者一覧のスタイル */
            .user-list {
                display: none;
                /* 初期状態は非表示 */
                background-color: #fefefe;
                padding: 20px;
                border: 1px solid #888;
                margin-top: 10px;
                color: black;
                /* テキストカラーを黒に設定 */
            }

            .user-list ul li {
                color: black;
                /* 利用者一覧のli要素のテキストカラーを黒に設定 */
                cursor: pointer;
                /* カーソルをポインターに設定 */
            }

            /* モーダルのスタイル */
            .modal {
                display: none;
                /* 初期状態は非表示 */
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgba(0, 0, 0, 0.4);
            }

            .modal-content {
                background-color: #fefefe;
                margin: 15% auto;
                padding: 20px;
                border: 1px solid #888;
                width: 80%;
                max-width: 500px;
                color: black;
                /* テキストカラーを黒に設定 */
            }

            .close {
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
            }

            .close:hover,
            .close:focus {
                color: black;
                text-decoration: none;
                cursor: pointer;
            }

            .modal-buttons {
                margin-top: 20px;
                text-align: center;
            }

            .modal-buttons button {
                margin: 0 10px;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

            .yes-button {
                background-color: #007BFF;
                /* 青色 */
                color: white;
            }

            .no-button {
                background-color: #f44336;
                /* 赤色 */
                color: white;
            }

            .link-button {
                background-color: #007BFF;
                /* 青色 */
                color: white;
            }

            .unlink-button {
                background-color: #f44336;
                /* 赤色 */
                color: white;
            }
        </style>

        <div class="flex">
            <!-- 左半分: 利用者情報 -->
            <div class="w-1/2 p-4 overflow-y-auto" style="max-height: 80vh;">
                @foreach($facilitypeople as $facilityperson)
                <div class="bg-white rounded-lg shadow-md p-4 mb-4 cursor-pointer user-card" data-family="{{ json_encode($facilityperson->people_family) }}" data-user="{{ json_encode(['id' => $facilityperson->id, 'last_name' => $facilityperson->last_name, 'first_name' => $facilityperson->first_name]) }}">
                    <h3 class="text-gray-900 font-bold text-lg">{{ $facilityperson->last_name }} {{ $facilityperson->first_name }}</h3>
                    <p class="text-gray-700">生年月日: {{ $facilityperson->date_of_birth }}</p>
                </div>
                @endforeach
            </div>

            <!-- 右半分: 家族情報 -->
            <div class="w-1/2 p-4">
                <div class="bg-white rounded-lg shadow-md p-4" id="family-info">
                    <h3 class="text-gray-900 font-bold text-xl mb-2">家族情報</h3>
                    <p>左側のリストから利用者を選択してください。</p>
                    <ul id="family-list" class="list-disc pl-5"></ul>
                    <button id="add-family-btn" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded" style="display: none;">家族を追加</button>
                    <div id="userList" class="user-list">
                        <h4 class="font-semibold">施設に紐づく利用者一覧:</h4>
                        <ul class="list-disc pl-5">
                            @foreach($facilitypeople as $facilityperson)
                            <li>
                                <input type="checkbox" class="select-user" data-user="{{ json_encode(['id' => $facilityperson->id, 'last_name' => $facilityperson->last_name, 'first_name' => $facilityperson->first_name]) }}">
                                {{ $facilityperson->last_name }} {{ $facilityperson->first_name }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- モーダル1 -->
        <div id="confirmationModal1" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <p id="confirmationMessage1"></p>
                <div class="modal-buttons">
                    <button class="yes-button">はい</button>
                    <button class="no-button">いいえ</button>
                </div>
            </div>
        </div>

        <!-- モーダル2 -->
        <div id="confirmationModal2" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <p id="confirmationMessage2"></p>
                <div class="modal-buttons">
                    <form id="linkForm" method="POST" action="/brother-invitation">
                        @csrf
                        <input type="hidden" id="person_id" name="person_id">
                        <input type="hidden" id="user_id" name="user_id">
                        <button type="submit" class="link-button">紐づける</button>
                    </form>
                    <button class="unlink-button">紐づけない</button>
                </div>
            </div>
        </div>

        <!-- Hidden inputs for person_id and user_id -->
        <input type="hidden" id="person_id" name="person_id">
        <input type="hidden" id="user_id" name="user_id">

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const userCards = document.querySelectorAll('.user-card');
                const familyList = document.getElementById('family-list');
                const addFamilyBtn = document.getElementById('add-family-btn');
                const userList = document.getElementById('userList');
                const confirmationModal1 = document.getElementById('confirmationModal1');
                const confirmationMessage1 = document.getElementById('confirmationMessage1');
                const confirmationModal2 = document.getElementById('confirmationModal2');
                const confirmationMessage2 = document.getElementById('confirmationMessage2');
                const closeModal = document.querySelectorAll('.close');
                const yesButton = document.querySelector('.yes-button');
                const noButton = document.querySelector('.no-button');
                const linkButton = document.querySelector('.link-button');
                const unlinkButton = document.querySelector('.unlink-button');
                const personIdInput = document.getElementById('person_id');
                const userIdInput = document.getElementById('user_id');
                let selectedFamilyName = '';
                let selectedUserData = null;
                let currentCheckbox = null;
                let selectedFamilyId = null;
                //before
                // userCards.forEach(card => {
                //     card.addEventListener('click', function() {
                //         const familyData = JSON.parse(this.getAttribute('data-family'));
                //         console.log(familyData);
                //         const userData = JSON.parse(this.getAttribute('data-user'));
                //         console.log(userData);
                //         familyList.innerHTML = ''; // リストをクリア

                //         if (familyData && familyData.length > 0) {
                //             familyData.forEach(family => {
                //                 const li = document.createElement('li');
                //                 li.textContent = `${family.last_name} ${family.first_name}`;
                //                 familyList.appendChild(li);

                //                 // 登録済利用者を追加
                //                 const subList = document.createElement('ul');
                //                 subList.classList.add('pl-5');
                //                 const subLi = document.createElement('li');
                //                 subLi.textContent = `登録済利用者: ${userData.last_name} ${userData.first_name}、利用者 三郎`;
                //                 subLi.style.color = 'black'; // テキストカラーを黒に設定
                //                 subList.appendChild(subLi);
                //                 familyList.appendChild(subList);

                //                 // 家族名とIDを保存
                //                 selectedFamilyName = `${family.last_name} ${family.first_name}`;
                //                 selectedFamilyId = family.id; // 家族のIDを保存
                //             });

                //             // 家族情報が存在する場合にボタンとリストを表示
                //             addFamilyBtn.style.display = 'block';
                //             userList.style.display = 'block';
                //         } else {
                //             const li = document.createElement('li');
                //             li.textContent = '登録された家族はいません';
                //             familyList.appendChild(li);

                //             // 家族情報が存在しない場合にボタンとリストを非表示
                //             addFamilyBtn.style.display = 'none';
                //         }

                //         // 利用者一覧は「家族を追加」ボタンをクリックするまで非表示
                //         userList.style.display = 'none';
                //     });
                // });
                //after
                userCards.forEach(card => {
                    card.addEventListener('click', function() {
                        const familyData = JSON.parse(this.getAttribute('data-family'));
                        const userData = JSON.parse(this.getAttribute('data-user'));
                        familyList.innerHTML = ''; // リストをクリア

                        if (familyData && familyData.length > 0) {
                            familyData.forEach(family => {
                                const li = document.createElement('li');
                                li.textContent = `${family.last_name} ${family.first_name}`;
                                familyList.appendChild(li);

                                // 登録済み利用者のリストを作成
                                const subList = document.createElement('ul');
                                subList.classList.add('pl-5');

                                // この家族に紐づけられている全ての利用者を表示
                                if (family.registered_people && family.registered_people.length > 0) {
                                    const subLi = document.createElement('li');
                                    const registeredUsers = family.registered_people
                                        .map(person => `${person.last_name} ${person.first_name}`)
                                        .join('、');
                                    subLi.textContent = `登録済利用者: ${registeredUsers}`;
                                    subLi.style.color = 'black';
                                    subList.appendChild(subLi);
                                }
                                familyList.appendChild(subList);


                                selectedFamilyName = `${family.last_name} ${family.first_name}`;
                                selectedFamilyId = family.id;

                                // チェックボックス表示時に登録済み利用者を除外
                                const registeredUserIds = family.registered_people ?
                                    family.registered_people.map(person => person.id) : [];

                                // userListの更新
                                const checkboxes = userList.querySelectorAll('.select-user');
                                checkboxes.forEach(checkbox => {
                                    const checkboxUserData = JSON.parse(checkbox.getAttribute('data-user'));
                                    if (registeredUserIds.includes(checkboxUserData.id)) {
                                        checkbox.closest('li').style.display = 'none';
                                    } else {
                                        checkbox.closest('li').style.display = 'block';
                                    }
                                });
                            });

                            addFamilyBtn.style.display = 'block';
                        } else {
                            const li = document.createElement('li');
                            li.textContent = '登録された家族はいません';
                            familyList.appendChild(li);
                            addFamilyBtn.style.display = 'none';

                        }

                        userList.style.display = 'none';
                    });
                });

                addFamilyBtn.addEventListener('click', function() {
                    userList.style.display = userList.style.display === 'none' || userList.style.display === '' ? 'block' : 'none';
                });

                // 利用者を選択したときの処理
                document.querySelectorAll('.select-user').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            currentCheckbox = this;
                            selectedUserData = JSON.parse(this.getAttribute('data-user'));
                            confirmationMessage1.textContent = `利用者${selectedUserData.last_name} ${selectedUserData.first_name}さんをご家族${selectedFamilyName}さんと紐づけますか？`;
                            confirmationModal1.style.display = 'block';

                            // Hidden inputsに値を設定
                            personIdInput.value = selectedUserData.id;
                            userIdInput.value = selectedFamilyId;
                        }
                    });
                });

                yesButton.addEventListener('click', function() {
                    confirmationModal1.style.display = 'none';
                    confirmationMessage2.textContent = `紐づけると利用者${selectedUserData.last_name} ${selectedUserData.first_name}さんの情報を${selectedFamilyName}さんが確認できるようになります。`;
                    confirmationModal2.style.display = 'block';
                });

                noButton.addEventListener('click', function() {
                    confirmationModal1.style.display = 'none';
                    if (currentCheckbox) {
                        currentCheckbox.checked = false; // チェックを解除
                    }
                });

                linkButton.addEventListener('click', function(event) {
                    // フォームを送信
                    document.getElementById('linkForm').submit();
                });

                unlinkButton.addEventListener('click', function() {
                    confirmationModal2.style.display = 'none';
                    if (currentCheckbox) {
                        currentCheckbox.checked = false; // チェックを解除
                    }
                });

                closeModal.forEach(close => {
                    close.addEventListener('click', function() {
                        confirmationModal1.style.display = 'none';
                        confirmationModal2.style.display = 'none';
                        if (currentCheckbox) {
                            currentCheckbox.checked = false; // チェックを解除
                        }
                    });
                });

                window.addEventListener('click', function(event) {
                    if (event.target == confirmationModal1) {
                        confirmationModal1.style.display = 'none';
                        if (currentCheckbox) {
                            currentCheckbox.checked = false; // チェックを解除
                        }
                    }
                    if (event.target == confirmationModal2) {
                        confirmationModal2.style.display = 'none';
                        if (currentCheckbox) {
                            currentCheckbox.checked = false; // チェックを解除
                        }
                    }
                });
            });
        </script>
    </body>
</x-app-layout>