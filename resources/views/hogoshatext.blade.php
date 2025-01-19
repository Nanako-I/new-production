<x-app-layout>
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline font-bold">{{ session('success') }}</span>
        </div>
    @endif
    
   <div id="chatbot">
   <div class="flex items-center justify-center my-2 text-gray-900 font-bold text-2xl">
   <h2 class="contact-header">{{$person->last_name}}{{$person->first_name}}さんについての連絡</h2>
    </div>
        
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


            /* @media screen and (max-width: 700px) {
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
            } */
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

            

            
            .text-sm {
                font-size: 0.7rem;
            }

            .font-normal {
                font-weight: normal;
            }

            #notebook {
                width: 95%;
                height: 60px; /* footer内での高さを設定 */
                box-sizing: border-box;
                padding: 10px;
                border: 1px solid #ccc;
                background-color: #fff;
            }

            /* PC画面用のスタイル */
           
                #notebook {
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
        <form action="{{ route('hogoshatext.show', $person->id) }}" method="GET" class="w-full max-w-lg mx-auto mt-4">
            @csrf
            <div class="flex flex-col items-center my-4">
                @error('notebook')
                    <p class="text-red-500 text-lg font-bold mt-2">{{ $message }}</p>
                @enderror
            </div>
        </form>

        <div id="hogoshatexts-container" class="hogoshatexts-container mt-4 w-full max-w-lg mx-auto">
        <div id="chatbot-body">
        <ul id="chatbot-ul" class="chat-messages">
            @section('hogoshatexts')
                @if($hogoshatexts->isNotEmpty())
                    @foreach($hogoshatexts as $hogosha_text)
                        <div class="p-4 rounded-lg shadow mb-4 w-full {{ $hogosha_text->user_identifier != session('user_identifier') ? 'bg-blue-50 border-2 border-blue-500' : 'bg-white' }}">
                            @if($hogosha_text->user_identifier != session('user_identifier'))
                                <p class="text-sm text-gray-700 font-bold mt-2"> {{ $hogosha_text->last_name . $hogosha_text->first_name }}</p>
                            @endif
                            <p class="text-gray-900 font-bold">{{ $hogosha_text->notebook }}</p>
                            <p class="text-sm text-gray-700 mt-2 font-bold">記録時間: {{ $hogosha_text->created_at->format('H:i') }}</p>
                            
                        </div>
                    @endforeach
                
                @endif
            @show
        </ul>
            </div>
        </div>
        <div id="chatbot-footer" class="justify-center">
            <form action="{{ route('hogoshatext.store', $person->id) }}" method="POST" class="w-full mx-auto mt-4">
                @csrf
                <div class="flex items-center my-4 w-full gap-4">
                    @error('notebook')
                        <p class="text-red-500 text-lg font-bold mt-2">{{ $message }}</p>
                    @enderror
                    <div class="textarea-container">
                        <textarea name="notebook" id="notebook" class="notebook font-bold p-4 rounded-lg shadow">{{ old('notebook') }}</textarea>
                        <button type="submit" id="send-button" class="font-semibold">送信</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
 
       

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        // チャットボックスを下までスクロールする関数
        function chatToBottom() {
            const chatField = document.getElementById('chatbot-body');
            chatField.scrollTop = chatField.scrollHeight;
        }

        chatToBottom();

        const chatForm = document.getElementById('chat-form');
        if (chatForm) {
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(chatForm);

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
                    document.getElementById('chatbot-text').value = '';
                    document.getElementById('filename').value = '';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('メッセージの送信に失敗しました。');
                });
            }, { once: true });
        }
    });
</script>

</x-app-layout>

