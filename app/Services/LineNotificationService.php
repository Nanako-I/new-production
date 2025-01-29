<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LineNotificationService
{
    protected $accessToken;

    public function __construct()
    {
        // $this->accessToken = env('LINE_CHANNEL_ACCESS_TOKEN'); // 環境変数から取得
        // env()の代わりにconfig()を使用
        $this->accessToken = config('services.line.channel_access_token');
    }

    public function sendNotification($lineUserId, $message)
    {
        Log::info('LINE User ID: ' . $lineUserId);

        if (empty($lineUserId)) {
            throw new \Exception('LINE User ID is not set.');
        }
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

        // レスポンスをログに出力
        if ($response->successful()) {
            Log::info('LINE API Response: ', $response->json());
        } else {
            Log::error('Failed to send LINE notification', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }

        return $response->successful();
    }
} 