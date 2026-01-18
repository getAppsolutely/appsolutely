<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class NotificationQueue extends Model
{
    protected $table = 'notification_queue';

    protected $fillable = [
        'rule_id',
        'template_id',
        'sender_id',
        'recipient_email',
        'subject',
        'body_html',
        'body_text',
        'trigger_data',
        'status',
        'scheduled_at',
        'sent_at',
        'error_message',
        'attempts',
    ];

    protected $casts = [
        'trigger_data' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at'      => 'datetime',
        'attempts'     => 'integer',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(NotificationRule::class, 'rule_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class, 'template_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(NotificationSender::class, 'sender_id');
    }

    /**
     * Scope for pending notifications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for sent notifications
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope for failed notifications
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for ready to send notifications
     */
    public function scopeReadyToSend($query)
    {
        return $query->where('status', 'pending')
            ->where('scheduled_at', '<=', now());
    }

    /**
     * Check if notification is ready to send
     */
    public function getIsReadyToSendAttribute(): bool
    {
        return $this->status === 'pending' && $this->scheduled_at <= now();
    }

    /**
     * Mark as sent
     */
    public function markAsSent(): void
    {
        $this->update([
            'status'  => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status'        => 'failed',
            'error_message' => $errorMessage,
            'attempts'      => $this->attempts + 1,
        ]);
    }

    /**
     * Retry failed notification
     */
    public function retry(): void
    {
        $this->update([
            'status'        => 'pending',
            'scheduled_at'  => now(),
            'error_message' => null,
        ]);
    }

    /**
     * Get formatted trigger data
     */
    public function getFormattedTriggerDataAttribute(): string
    {
        if (empty($this->trigger_data)) {
            return 'No data';
        }

        return collect($this->trigger_data)
            ->map(fn ($value, $key) => "{$key}: " . (is_array($value) ? json_encode($value) : $value))
            ->implode(', ');
    }
}
