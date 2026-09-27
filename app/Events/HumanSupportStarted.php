<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HumanSupportStarted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Conversation $conversation;
    public string $reason;

    public function __construct(Conversation $conversation, string $reason = 'Escalated to human support')
    {
        $this->conversation = $conversation;
        $this->reason       = $reason;
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
        return 'human.support.started';
    }

    /**
     * Data to broadcast with event payload.
     */
    public function broadcastWith(): array
    {
        return [
            'conversation_uuid' => (string) $this->conversation->uuid,
            'mode'              => 'human',
            'status'            => (string) $this->conversation->status,
            'reason'            => $this->reason,
            'message'           => 'A human support representative has been assigned to your conversation.',
            'started_at'        => now()->toDateTimeString(),
        ];
    }
}
