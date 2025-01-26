<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LineNotificationService
{
    protected $accessToken;

    public function __construct()
    {
        $this->accessToken = env('LINE_CHANNEL_ACCESS_TOKEN'); // 環境変数から取得
    }

    public function sendNotification($lineUserId, $message)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->post('https://api.line.me/v2/bot/message/push', [
            'to' => $lineUserId,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $message,
                ],
            ],
        ]);

        return $response->successful();
    }
} 