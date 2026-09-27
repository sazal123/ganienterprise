<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public string $conversationUuid;
    public int $unreadCount;

    public function __construct(Message $message, ?Conversation $conversation = null, int $unreadCount = 0)
    {
        $this->message          = $message;
        $conv                   = $conversation ?? $message->conversation;
        $this->conversationUuid = $conv ? (string) $conv->uuid : '';
        $this->unreadCount      = $unreadCount;
    }

    /**
     * Get the private channel the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel("chat.conversation.{$this->conversationUuid}");
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.created';
    }

    /**
     * Data to broadcast with event payload.
     */
    public function broadcastWith(): array
    {
        return [
            'id'                => (int) $this->message->id,
            'conversation_id'   => (int) $this->message->conversation_id,
            'conversation_uuid' => $this->conversationUuid,
            'sender_type'       => (string) $this->message->sender_type,
            'sender_id'         => $this->message->sender_id ? (int) $this->message->sender_id : null,
            'message_type'      => (string) $this->message->message_type,
            'message'           => (string) $this->message->message,
            'created_at'        => $this->message->created_at ? $this->message->created_at->toDateTimeString() : null,
            'unread_count'      => $this->unreadCount,
        ];
    }
}
