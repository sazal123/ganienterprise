<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationClosed implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Conversation $conversation;

    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;
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
        return 'conversation.closed';
    }

    /**
     * Data to broadcast with event payload.
     */
    public function broadcastWith(): array
    {
        return [
            'conversation_uuid' => (string) $this->conversation->uuid,
            'status'            => 'closed',
            'closed_at'         => now()->toDateTimeString(),
            'message'           => 'This conversation has been closed.',
        ];
    }
}
