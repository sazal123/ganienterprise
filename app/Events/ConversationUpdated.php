<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Conversation $conversation;
    public int $unreadCount;

    public function __construct(Conversation $conversation, int $unreadCount = 0)
    {
        $this->conversation = $conversation;
        $this->unreadCount  = $unreadCount;
    }

    /**
     * Get the private channel the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel("chat.conversation.{$this->conversation->uuid}");
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'conversation.updated';
    }

    /**
     * Data to broadcast with event payload.
     */
    public function broadcastWith(): array
    {
        return [
            'id'              => (int) $this->conversation->id,
            'uuid'            => (string) $this->conversation->uuid,
            'status'          => (string) $this->conversation->status,
            'mode'            => (string) $this->conversation->mode,
            'last_message_at' => $this->conversation->last_message_at ? $this->conversation->last_message_at->toDateTimeString() : null,
            'unread_count'    => $this->unreadCount,
        ];
    }
}
