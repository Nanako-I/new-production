import _ from 'lodash';
window._ = _;

import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    wsHost: import.meta.env.VITE_PUSHER_HOST || `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
    wsPort: import.meta.env.VITE_PUSHER_PORT || 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT || 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME || 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

Pusher.logToConsole = true;

// チャットUIを更新する関数
function updateChatUI(message) {
    console.log('Updating chat UI with:', message);
    const chatContainer = document.getElementById('chatbot-ul');
    if (chatContainer) {
        const messageElement = document.createElement('li');
        const chatbotContainer = document.getElementById('chatbot');
        const sessionUserIdentifier = chatbotContainer.dataset.userIdentifier;
        const sessionUserName = chatbotContainer.dataset.userName;

        messageElement.className = message.user_identifier === sessionUserIdentifier ? 'self' : 'other';
        messageElement.innerHTML = `
            <div class="message-container ${messageElement.className}-message">
                <div style="overflow-wrap: break-word;">
                    <p style="overflow-wrap: break-word;" class="text-gray-900">${message.message}</p>
                    ${message.filename ? `<img alt="team" class="w-80 h-64" src="/storage/sample/chat_photo/${message.filename}" onerror="this.onerror=null;">` : ''}
                </div>
                <p class="text-sm font-normal ${messageElement.className === 'self' ? 'text-right' : 'text-left'}">
                    ${message.created_at} ＠${message.user_identifier === sessionUserIdentifier ? sessionUserName : message.last_name + message.first_name}
                </p>
            </div>
        `;
        chatContainer.appendChild(messageElement);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    } else {
        console.warn('チャットコンテナが見つかりません');
    }
}

// DOMContentLoaded イベントリスナー
document.addEventListener('DOMContentLoaded', function() {
    if (!window.pusherInitialized) {
        window.pusherInitialized = true; // フラグを設定して、Pusherの初期化が1回だけ行われるようにする

        const chatbotContainer = document.getElementById('chatbot');
        
        if (chatbotContainer && window.Echo) {
            window.Echo.channel('chat-1')
                .subscribed(() => {
                    console.log('Subscribed to chat-1 channel');
                })
                .listen('.MessageSent', (e) => {
                    console.log('新しいメッセージを受信:', e);
                    updateChatUI(e);
                })
                .error((error) => {
                    console.error('Channel error:', error);
                });

            window.Echo.connector.pusher.connection.bind('state_change', function(states) {
                console.log('Pusher接続状態:', states.current);
            });
        } else {
            console.warn('Echo が定義されていないか、チャットボットコンテナが見つかりません');
        }
    }
});

function scrollToBottom() {
    const chatContainer = document.getElementById('chatbot-body');
    if (chatContainer) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
}

// Call scrollToBottom when the page loads
// document.addEventListener('DOMContentLoaded', scrollToBottom);