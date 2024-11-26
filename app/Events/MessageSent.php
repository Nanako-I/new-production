<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Chat  $chat
     * @return void
     */
    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\Channel[]
     */
    public function broadcastOn()
    {
        \Log::info('Broadcasting on channel: chat-' . $this->chat->people_id);
        return new Channel('chat-'.$this->chat->people_id); // Channelを使用
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->chat->id,
            'people_id' => $this->chat->people_id,
            'user_name' => $this->chat->user_name,
            'user_identifier' => $this->chat->user_identifier,
            'message' => $this->chat->message,
            'filename' => $this->chat->filename,
            'path' => $this->chat->path,
            'created_at' => $this->chat->created_at->toDateTimeString(),
            'last_name' => $this->chat->last_name,
            'first_name' => $this->chat->first_name,
        ];
    }
}
