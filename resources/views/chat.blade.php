<x-app-layout>
    
        <head>
            @vite(['resources/css/app.css', 'resources/js/app.js'])
            <meta name="csrf-token" content="{{ csrf_token() }}">
      
            <!-- <script>
                const peopleId = "{{ $person->id }}";
                window.sessionUserIdentifier = "{{ session('user_identifier') }}";
                window.peopleId = "{{ $person->id }}";

                window.UserId={{ auth()->id()}};
            </script> -->
            <!-- <script src="https://js.pusher.com/7.0/pusher.min.js"></script> -->
            <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                user-select: none;
            }

            body {
                box-sizing: border-box;
                background: #FFF;
                font-family: 'Noto Sans JP', -apple-system, BlinkMacSystemFont, "Helvetica Neue", YuGothic, "ヒラギノ角ゴ ProN W3", Hiragino Kaku Gothic ProN, Arial, "メイリオ", Meiryo, sans-serif;
                -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
                tap-highlight-color: rgba(0, 0, 0, 0);
                overflow: hidden;
            }

            a {
                color: #2196F3;
                text-decoration: none;
            }

            h2 {
                font-family: Arial, sans-serif;
                font-size: 20px;
            }

            .contact-header {
            display: block; /* 確実に表示されるようにする */
            color: black; /* 必要に応じて色を設定 */
        }

            #chatbot {
                position: fixed;
                overflow: hidden;
                opacity: 1;
                transition: .4s;
                background: #FFF;
                -webkit-font-smoothing: none;
                -webkit-font-smoothing: antialiased;
                -webkit-font-smoothing: subpixel-antialiased;
                height: 100vh;
                width: 100vw;
            }

            @media screen and (min-width: 700px) {
                #chatbot {
                    height: 80vh;
                    width: 100%;
                    bottom: 0;
                    right: 0;
                    margin: 0;
                    box-shadow: 0px 0 25px -5px #888;
                    border-radius: 10px;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    -webkit-transform: translate(-50%, -50%);
                    -moz-transform: translate(-50%, -50%);
                }
            }

            @media screen and (max-width: 700px) {
                #chatbot {
                    height: 100vh;
                    width: 100vw;
                }
            }


            #chatbot-body {
                width: 100%;
                height: calc(100vh - 150px);
                padding-top: 10px;
                padding-bottom: 10px;
                background: #FFF;
                box-sizing: border-box;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                position: absolute;
                top: 0;
                bottom: 80px;
            }


        
            #chatbot-body {
                width: 100%;
                height: calc(100vh - 240px); /* 画面の高さから80pxを引いた値 */
                padding-top: 10px;
                padding-bottom: 10px;
                background: #FFF;
                box-sizing: border-box;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                background: linear-gradient(135deg, rgb(209, 253, 255,0.5), rgb(253, 219, 146,1));
            }

            #chatbot-body.chatbot-body-zoom {
                width: 100%;
            }

            #chatbot-body.chatbot-body-zoom {
                height: calc(100vh - 60px);
            }

            #chatbot-body::-webkit-scrollbar {
                display: none;
            }

            .chat-header {
                padding: 10px;
                background-color: #f0f0f0;
                text-align: center;
                font-weight: bold;
                z-index: 1000;
            }

            #chatbot-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 80px; /* 固定の高さを設定 */
                /* margin: 10px 30px; */
                background-color: #f9f9f9;
                border-top: 1px solid #ccc;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            @media screen and (min-width: 700px) {
                #chatbot-footer {
                    width: 100%;
                    margin: 0 40;
                }
            }

            @media screen and (min-width: 700px) {
                #chatbot-footer.chatbot-footer-zoom {
                    width: 100%;
                    margin-bottom: 0;
                }
            }

            @media screen and (max-width: 700px) {
                #chatbot-footer.chatbot-footer-zoom {
                    width: 100%;
                    position: fixed;
                    margin-bottom: 0;
                }
            }

            #chat-form {
                display: flex;
                width: 100%;
                justify-content: space-between;
                align-items: center;
            }

            #chatbot-text {
                height: 40px;
                width: 70%;
                display: block;
                font-size: 16px;
                box-sizing: border-box;
                padding-left: 10px;
                margin: auto 10px auto 15px;
                color: #777;
                border: 0;
                outline: 0;
            }

            #chatbot-text:focus {
                border: none;
                outline: none;
            }

            
            .textarea-container {
                    position: relative;
                    width: 100%;
                }
                        

            #chatbot-ul {
                padding: 0;
                list-style: none;
                max-height: 80vh;
                padding-bottom: 80px;
            }

            @media screen and (min-width: 700px) {
                #chatbot-ul {
                    max-width: 80%;
                    margin: 15%;
                    
                }
            }

            #chatbot-ul > li {
                position: relative;
                width: 100%;
                padding-bottom: 10px;
                word-wrap: break-word;
                display: flex;
            }

            .message-container {
                height: calc(100vh - 100px); /* 画面の高さからfooterの高さを引く */
                overflow-y: auto; /* 縦スクロールを有効にする */
                padding-bottom: 20px; /* footerとの間にスペースを作る */
            }

            .other-message {
                background-color: #ebf8ff; /* bg-blue-50 */
                border: 2px solid #3b82f6; /* border-blue-500 */
            }

            .self-message {
                    background-color: #ffffff; /* bg-white */
                    border: 2px solid #808080; /* 灰色のボーダー */
                }

            

            
            .text-sm {
                font-size: 0.7rem;
            }

            .font-normal {
                font-weight: normal;
            }

            #chatbot-text {
                width: 95%;
                height: 60px; /* footer内での高さを設定 */
                box-sizing: border-box;
                padding: 10px;
                border: 1px solid #ccc;
                background-color: #fff;
            }

            /* PC画面用のスタイル */
           
                #chatbot-text {
                    width: 100%; /* PC画面では幅を100%に設定 */
                }

                #send-button {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        height: 40px;
        padding: 0 20px;
        border: none;
        background-color: #000; /* 背景を黒に設定 */
        color: #fff; /* 文字色を白に設定 */
        font-size: 16px; /* 文字サイズを大きく設定 */
        cursor: pointer;
        border-radius: 5px;
    }

    #send-button:hover {
        background-color: #333; /* ホバー時の背景色を少し明るく */
    }
</style>
    </head>
    <body class="font-sans antialiased" >
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <div id="chatbot"
            data-user-identifier="{{ session('user_identifier') }}"
            data-user-name="{{ session('user_name') }}">>
            <div class="flex flex-col items-center">
                <form action="{{ url('people' ) }}" method="POST" class="w-full max-w-lg">
                    @method('PATCH')
                    @csrf
                    <div class="flex items-center justify-center text-center">
                        <h2 class="text-center">{{$person->last_name}}{{$person->first_name}}さんについての連絡</h2>
                    </div>
                </form>
            </div>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
            <script src="https://kit.fontawesome.com/de653d534a.js" crossorigin="anonymous"></script>
            <div id="chatbot-body">
            <ul id="chatbot-ul" class="chat-messages">
                    @foreach ($chats as $chat)
                    <li class="{{ $chat->user_identifier == session('user_identifier') ? 'self' : 'other' }}">
                        <div class="p-4 rounded-lg shadow mb-4 w-full {{ $chat->user_identifier == session('user_identifier') ? 'self-message' : 'other-message' }}">
                        
                            <div style="overflow-wrap: break-word;">
                                <p style="overflow-wrap: break-word;" class="text-gray-900 font-bold">{{ $chat->message }}</p>
                                @if($chat->filename)
                                    <img alt="team" class="w-80 h-64" 
                                        
                                        src="{{ asset('storage/sample/chat_photo/' . $chat->filename) }}"
                                        onerror="this.onerror=null;">
                                        

                                @endif
                            </div>
                            <p class="font-normal text-black {{ $chat->user_identifier == session('user_identifier') }}">
                                {{ $chat->created_at }} ＠{{ $chat->user_identifier == session('user_identifier') ? session('user_name') : $chat->last_name . $chat->first_name }}
                            </p>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div id="chatbot-footer">
        <form id="chat-form" action="{{ url('chat/'.$person->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
                <input type="hidden" name="user_identifier" value="{{ session('user_identifier') }}">
                <input type="hidden" name="last_name" value="{{ $user_name }}">
                <label for="filename" class="hidden" style="cursor: pointer;">
                    <i class="fa-regular fa-image mt-2" style="font-size: 2em;"></i>
                    <input name="filename" id="filename" type="file" style="display: none;" onChange="uploadFile1()">
                </label>
                
                <div class="textarea-container">
                    
                    <input type="text" id="chatbot-text" class="browser-default" name="message" placeholder="テキストを入力" required
                        style="word-wrap: break-word;" data-user-identifier="{{ session('user_identifier') }}">
                    <button type="submit" id="send-button" class="font-semibold">送信</button>
                </div>
           
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', (event) => {
        // チャットボックスを下までスクロールする関数
        function chatToBottom() {
            const chatField = document.getElementById('chatbot-body');
            chatField.scrollTop = chatField.scrollHeight;
        }

        chatToBottom();

    //     const chatForm = document.getElementById('chat-form');
    //     if (chatForm) {
    //         chatForm.addEventListener('submit', function(e) {
    //             e.preventDefault();
    //             var formData = new FormData(chatForm);

    //             const isFileSelected = document.getElementById('filename').files.length > 0;

    //             if (isFileSelected) {
    //                 formData.append('message', '写真が送信されました');
    //             }

    //             fetch(this.action, {
    //                 method: 'POST',
    //                 body: formData
    //             })
    //             .then(response => {
    //                 if (!response.ok) {
    //                     throw new Error('Network response was not ok');
    //                 }
    //                 return response.json();
    //             })
    //             .then(data => {
    //                 console.log('Data:', data);
    //                 if (data.error) {
    //                     throw new Error(data.error);
    //                 }
    //                 document.getElementById('chatbot-text').value = '';
    //                 document.getElementById('filename').value = '';
    //             })
    //             .catch(error => {
    //                 console.error('Error:', error);
    //                 alert('メッセージの送信に失敗しました。');
    //             });
    //         }, { once: true });
    //     }
    // });

    // document.addEventListener('DOMContentLoaded', function() {
    //     var chatForm = document.getElementById('chat-form');
    //     var fileInput = document.getElementById('filename');

    //     chatForm.addEventListener('submit', function(event) {
    //         event.preventDefault();
    //         if (fileInput && fileInput.files.length > 0) {
    //             console.log('ファイルが選択されています');
    //         } else {
    //             console.log('ファイルが選択されていません');
    //         }
    //     });
    });
</script>
</body>
</x-app-layout>