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
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
    encrypted: true,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

Pusher.logToConsole = true;

// チャットUIを更新する関数
function updateChatUI(message) {
    console.log('Updating chat UI with:', message);
    const chatContainer = document.getElementById('chat-messages');
    if (chatContainer) {
        const messageElement = document.createElement('div');
        messageElement.className = 'message';
        messageElement.innerHTML = `
            <strong>${message.last_name} ${message.first_name}:</strong>
            <span>${message.message}</span>
            <small>${message.created_at}</small>
        `;
        chatContainer.appendChild(messageElement);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    } else {
        console.warn('チャットコンテナが見つかりません');
    }
}

if (window.Echo) {
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

    // Pusher接続状態の変更をログに記録
    window.Echo.connector.pusher.connection.bind('state_change', function(states) {
        console.log('Pusher接続状態:', states.current);
    });
}