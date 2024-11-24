<x-app-layout>
        <!--ヘッダー[START]-->
        <head>
            @vite(['resources/css/app.css', 'resources/js/app.js'])
            <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> -->
        <!-- <script src="{{ asset('js/app.js') }}" defer></script> -->
            <script>
                const peopleId = "{{ $person->id }}";
                window.sessionUserIdentifier = "{{ session('user_identifier') }}";
                window.peopleId = "{{ $person->id }}";

                window.UserId={{ auth()->id()}};
            </script>
            <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
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
                /* height: calc(100vh - 110px); */
                height: calc(100vh - 60px);
                padding-top: 10px;
                padding-bottom: 80px;
                background: #FFF;
                box-sizing: border-box;
                overflow-x: hidden;
                /* overflow-y: scroll; */
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                -ms-overflow-style: none;
                position: fixed;
                bottom: 80px; /* フッターの高さ */
                top: 80PX;
            }

            @media screen and (max-width: 700px) {
                #chatbot-body {
                    height: calc(100vh - 60px);
                }
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
                width: 100%;
                height: 60px;
                display: flex;
                justify-content: center;
                align-items: center;
                box-sizing: border-box;
                background: #FFF;
                border-top: 1.5px solid #EEE;
                position: fixed;
                bottom: 0;
                left: 0;
                padding: 10px;
                z-index: 1000;
            }

            @media screen and (min-width: 700px) {
                #chatbot-footer {
                    width: 100%;
                    margin: 0 auto;
                }
            }

            @media screen and (min-width: 700px) {
                #chatbot-footer.chatbot-footer-zoom {
                    margin-bottom: 0;
                }
            }

            @media screen and (max-width: 700px) {
                #chatbot-footer.chatbot-footer-zoom {
                    position: fixed;
                    margin-bottom: 0;
                }
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

            #chatbot-submit {
                cursor: pointer;
                height: 35px;
                padding: 0 20px;
                margin-left: 10px;
                margin-right: 15px;
                font-size: 16px;
                background: #335C80;
                color: white;
                display: block;
                border: none;
                box-sizing: border-box;
                border-radius: 7px;
            }

            #chatbot-submit:active {
                outline: 0;
                background: #86ABBF;
            }

            #chatbot-ul {
                padding: 0;
                list-style: none;
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

            .self {
                justify-content: flex-end;
            }

            .other {
                justify-content: flex-start;
            }

            .message-container {
                overflow-wrap: break-word;
                max-width: 70%;
                margin: 0.5rem 1rem;
                padding: 0.5rem;
                font-size: 1.125rem;
                font-weight: bold;
                border-radius: 0.5rem;
                display: inline-block;
            }

            .self-message {
                background-color: #c3e1ff;
                color: #1a202c;
                border-color: #4299e1;
                text-align: left;
            }

            .other-message {
                background-color: #b2f5ea;
                color: #1a202c;
                border-color: #000;
                text-align: left;
            }

            .text-right {
                text-align: right;
            }

            .text-left {
                text-align: left;
            }

            .text-sm {
                font-size: 0.7rem;
            }

            .font-normal {
                font-weight: normal;
            }

        </style>

    </head>
    <body class="font-sans antialiased" style="margin-bottom: 60px;">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <div id="chatbot">
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
                <ul id="chatbot-ul">
                    @foreach ($chats as $chat)
                    <li class="{{ $chat->user_identifier == session('user_identifier') ? 'self' : 'other' }}">
                        <div class="message-container {{ $chat->user_identifier == session('user_identifier') ? 'self-message' : 'other-message' }}">
                            <div style="overflow-wrap: break-word;">
                                <p style="overflow-wrap: break-word;" class="text-gray-900">{{ $chat->message }}</p>
                                @if($chat->filename)
                                    <img alt="team" class="w-80 h-64" 
                                        
                                        src="{{ asset('storage/sample/chat_photo/' . $chat->filename) }}"
                                        onerror="this.onerror=null;">
                                        <!-- <img alt="team" class="w-80 h-64" 
                                        src="{{ asset('storage/sample/chat_photo/' . $chat->filename) }}"
                                        onerror="this.onerror=null;"> -->

                                @endif
                            </div>
                            <p class="text-sm font-normal {{ $chat->user_identifier == session('user_identifier') ? 'text-right' : 'text-left' }}">
                                {{ $chat->created_at }} ＠{{ $chat->user_identifier == session('user_identifier') ? session('user_name') : $chat->last_name . $chat->first_name }}
                            </p>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
        <form id="chat-form" action="{{ url('chat/'.$person->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="items-center" id="chatbot-footer">
                <input type="hidden" name="user_identifier" value="{{ session('user_identifier') }}">
                <input type="hidden" name="last_name" value="{{ $user_name }}">
                <label for="filename" style="cursor: pointer;">
                    <i class="fa-regular fa-image mt-2" style="font-size: 2em;"></i>
                    <input name="filename" id="filename" type="file" style="display: none;" onChange="uploadFile1()">
                </label>
                <input type="text" id="chatbot-text" class="browser-default" name="message" placeholder="テキストを入力" required
                        style="word-wrap: break-word;" data-user-identifier="{{ session('user_identifier') }}">
                <button type="submit" id="chatbot-submit">送信</button>
            </div>
        </form>

        <script>
            function uploadFile1() {
                var filename = document.getElementById('filename').value;
                if (filename.trim() !== '') {
                    document.getElementById('chatbot-text').value = '写真選択済。送信ボタンを押してください';
                }
            }

            function chatToBottom() {
                const chatField = document.getElementById('chatbot-body');
                chatField.scrollTop = chatField.scrollHeight;
            }

            document.addEventListener('DOMContentLoaded', (event) => {
                chatToBottom();
                const userIdentifier = document.getElementById('chatbot-text').getAttribute('data-user-identifier');

                window.displayMessage = function(data) {
                    const chatUl = document.getElementById('chatbot-ul');
                    const li = document.createElement('li');
                    const className = data.user_identifier == userIdentifier ? 'self' : 'other';
                    li.classList.add(className);
                    
                    const lastName = data.last_name || '';
                    const firstName = data.first_name || '';
                    const createdAt = data.created_at || '';
                    const message = data.message || '';
                    
                    li.innerHTML = `
                        <div class="message-container ${className === 'self' ? 'self-message' : 'other-message'}">
                            <div style="overflow-wrap: break-word;">
                                <p style="overflow-wrap: break-word;" class="text-gray-900">${message}</p>
                                ${data.filename ? `<img alt="team" class="w-80 h-64" src="/storage/sample/chat_photo/${data.filename}" onerror="this.onerror=null;">` : ''}
                            </div>
                            <p class="text-sm font-normal ${className === 'self' ? 'text-right' : 'text-left'}">
                                ${createdAt} ＠${lastName}${firstName}
                            </p>
                        </div>
                    `;
                    chatUl.appendChild(li);
                    chatToBottom();
                };

                document.getElementById('chat-form').addEventListener('submit', function(e) {
                    e.preventDefault();
                    var formData = new FormData(document.getElementById('chat-form'));

                    const isFileSelected = document.getElementById('filename').files.length > 0;
            
                    if (isFileSelected) {
                        formData.append('message', '写真が送信されました');
                    }

                    fetch(this.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Data:', data);
                        if (data.error) {
                            throw new Error(data.error);
                        }
                        window.displayMessage(data);
                        document.getElementById('chatbot-text').value = '';
                        document.getElementById('filename').value = '';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('メッセージの送信に失敗しました。');
                    });
                });
            });
        </script>
    <!-- </div> -->

    <!-- <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
     window.PUSHER_APP_KEY = "{{ env('PUSHER_APP_KEY') }}";
     window.PUSHER_APP_CLUSTER = "{{ env('PUSHER_APP_CLUSTER') }}";
    document.addEventListener('DOMContentLoaded', function() {
        Pusher.logToConsole = true;

        var pusher = new Pusher(window.PUSHER_APP_KEY, {
            cluster: window.PUSHER_APP_CLUSTER,
            encrypted: true,
            authEndpoint: '/broadcasting/auth', // Laravelのデフォルトの認証エンドポイント
    auth: {
        headers: {
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }
        });

        var channel = pusher.subscribe('private-chat-' + peopleId);
        channel.bind('message.sent', function(data) {
            console.log('Received message:', data);
            window.displayMessage(data);
        });

        window.displayMessage = function(data) {
            const chatUl = document.getElementById('chatbot-ul');
            const li = document.createElement('li');
            const className = data.user_identifier == window.sessionUserIdentifier ? 'self' : 'other';
            li.classList.add(className);

            li.innerHTML = `
                <div class="message-container ${className === 'self' ? 'self-message' : 'other-message'}">
                    <div style="overflow-wrap: break-word;">
                        <p style="overflow-wrap: break-word;" class="text-gray-900">${data.message}</p>
                        ${data.filename ? `<img alt="team" class="w-80 h-64" src="/storage/sample/chat_photo/${data.filename}" onerror="this.onerror=null;">` : ''}
                    </div>
                    <p class="text-sm font-normal ${className === 'self' ? 'text-right' : 'text-left'}">
                        ${data.created_at} ＠${data.user_name}
                    </p>
                </div>
            `;
            chatUl.appendChild(li);
            chatToBottom();
        };

        function chatToBottom() {
            const chatField = document.getElementById('chatbot-body');
            chatField.scrollTop = chatField.scrollHeight;
        }
    }); -->
<!-- </script> -->
</body>
</x-app-layout>