<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'customer_id',
        'guest_token_hash',
        'status',
        'mode',
        'assigned_admin_id',
        'subject',
        'last_message_at',
        'metadata',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Boot model events to auto-generate UUID.
     */
    protected static function booted()
    {
        static::creating(function ($conversation) {
            if (empty($conversation->uuid)) {
                $conversation->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Use UUID for route model binding.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Find conversation by UUID.
     *
     * @param string $uuid
     * @return static|null
     */
    public static function findByUuid(string $uuid): ?static
    {
        return static::where('uuid', $uuid)->first();
    }

    /**
     * Get user (admin/staff) associated with the conversation.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get storefront customer associated with the conversation.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get assigned admin/staff user.
     */
    public function assignedAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    /**
     * Get messages in this conversation.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }

    /**
     * Get active Telegram reply session for this conversation.
     */
    public function telegramReplySession()
    {
        return $this->hasOne(TelegramReplySession::class, 'conversation_id');
    }

    /**
     * Get AI usage logs for this conversation.
     */
    public function aiUsageLogs()
    {
        return $this->hasMany(AiUsageLog::class, 'conversation_id');
    }
}
