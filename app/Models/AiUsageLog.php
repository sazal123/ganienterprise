<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'conversation_id',
        'model',
        'input_tokens',
        'output_tokens',
        'total_tokens',
        'estimated_cost',
        'response_time_ms',
        'status',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:6',
    ];

    /**
     * Get associated conversation.
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    /**
     * Get user associated with log entry.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
