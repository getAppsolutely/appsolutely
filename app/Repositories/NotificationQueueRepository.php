<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\NotificationQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

final class NotificationQueueRepository extends BaseRepository
{
    public function model(): string
    {
        return NotificationQueue::class;
    }

    /**
     * Get pending queue items ready to send
     */
    public function getPendingToSend(): Collection
    {
        return $this->model->newQuery()->where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->where(function ($query) {
                $query->where('retry_count', '<', 3)
                    ->orWhereNull('retry_count');
            })
            ->with(['rule', 'template'])
            ->orderBy('scheduled_at')
            ->get();
    }

    /**
     * Get failed items that can be retried
     */
    public function getRetryable(): Collection
    {
        return $this->model->newQuery()->where('status', 'failed')
            ->where('retry_count', '<', 3)
            ->with(['rule', 'template'])
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Get queue items by status with pagination
     */
    public function getByStatus(string $status, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()->where('status', $status)
            ->with(['rule', 'template'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get all queue items with pagination and filtering
     */
    public function getPaginated(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['rule', 'template']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['template_id'])) {
            $query->where('template_id', $filters['template_id']);
        }

        if (! empty($filters['rule_id'])) {
            $query->where('rule_id', $filters['rule_id']);
        }

        if (! empty($filters['recipient_email'])) {
            $query->where('recipient_email', 'like', '%' . $filters['recipient_email'] . '%');
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Create queue item
     */
    public function createQueueItem(array $data): NotificationQueue
    {
        // Set default scheduled_at if not provided
        if (empty($data['scheduled_at'])) {
            $data['scheduled_at'] = now();
        }

        // Ensure proper data types
        if (isset($data['variables']) && is_string($data['variables'])) {
            $data['variables'] = json_decode($data['variables'], true);
        }

        if (isset($data['metadata']) && is_string($data['metadata'])) {
            $data['metadata'] = json_decode($data['metadata'], true);
        }

        return $this->create($data);
    }

    /**
     * Update queue item status
     */
    public function updateStatus(int $id, string $status, ?string $errorMessage = null): NotificationQueue
    {
        $data = ['status' => $status];

        if ($status === 'sent') {
            $data['sent_at'] = now();
        } elseif ($status === 'failed' && $errorMessage) {
            $data['error_message'] = $errorMessage;
        }

        $this->update($id, $data);

        return $this->find($id);
    }

    /**
     * Increment retry count
     */
    public function incrementRetry(int $id): NotificationQueue
    {
        $item       = $this->find($id);
        $retryCount = ($item->retry_count ?? 0) + 1;

        $this->update($id, [
            'retry_count' => $retryCount,
            'status'      => $retryCount >= 3 ? 'failed' : 'pending',
        ]);

        return $this->find($id);
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        return [
            'total'      => $this->model->newQuery()->count(),
            'pending'    => $this->model->newQuery()->where('status', 'pending')->count(),
            'sent'       => $this->model->newQuery()->where('status', 'sent')->count(),
            'failed'     => $this->model->newQuery()->where('status', 'failed')->count(),
            'today'      => $this->model->newQuery()->whereDate('created_at', today())->count(),
            'this_week'  => $this->model->newQuery()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => $this->model->newQuery()->whereMonth('created_at', now()->month)->count(),
        ];
    }

    /**
     * Get daily statistics for chart
     */
    public function getDailyStats(int $days = 30): array
    {
        $stats = $this->model->newQuery()->selectRaw('DATE(created_at) as date, status, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get();

        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date          = now()->subDays($i)->format('Y-m-d');
            $result[$date] = [
                'pending' => 0,
                'sent'    => 0,
                'failed'  => 0,
            ];
        }

        foreach ($stats as $stat) {
            if (isset($result[$stat->date])) {
                $result[$stat->date][$stat->status] = $stat->count;
            }
        }

        return $result;
    }

    /**
     * Get items by template
     */
    public function getByTemplate(int $templateId): Collection
    {
        return $this->model->newQuery()->where('template_id', $templateId)
            ->with('rule')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get items by rule
     */
    public function getByRule(int $ruleId): Collection
    {
        return $this->model->newQuery()->where('rule_id', $ruleId)
            ->with('template')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get items scheduled for future
     */
    public function getScheduled(): Collection
    {
        return $this->model->newQuery()->where('status', 'pending')
            ->where('scheduled_at', '>', now())
            ->with(['rule', 'template'])
            ->orderBy('scheduled_at')
            ->get();
    }

    /**
     * Cancel scheduled items
     */
    public function cancelScheduled(array $ids): int
    {
        return $this->model->newQuery()->whereIn('id', $ids)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);
    }

    /**
     * Retry failed items
     */
    public function retryFailed(array $ids = []): int
    {
        $query = $this->model->newQuery()->where('status', 'failed')
            ->where('retry_count', '<', 3);

        if (! empty($ids)) {
            $query->whereIn('id', $ids);
        }

        return $query->update([
            'status'        => 'pending',
            'scheduled_at'  => now(),
            'error_message' => null,
        ]);
    }

    /**
     * Retry a specific notification
     */
    public function retry(int $id): bool
    {
        $updated = $this->model->newQuery()->where('id', $id)
            ->where('status', 'failed')
            ->where('retry_count', '<', 3)
            ->update([
                'status'        => 'pending',
                'scheduled_at'  => now(),
                'error_message' => null,
            ]);

        return $updated > 0;
    }

    /**
     * Cancel a specific notification
     */
    public function cancel(int $id): bool
    {
        $updated = $this->model->newQuery()->where('id', $id)
            ->whereIn('status', ['pending', 'processing'])
            ->update(['status' => 'cancelled']);

        return $updated > 0;
    }

    /**
     * Clean old sent items
     */
    public function cleanOldSent(int $daysOld = 90): int
    {
        return $this->model->newQuery()->where('status', 'sent')
            ->where('sent_at', '<', now()->subDays($daysOld))
            ->delete();
    }

    /**
     * Get recent activity
     */
    public function getRecentActivity(int $limit = 10): Collection
    {
        return $this->model->newQuery()->with(['rule', 'template'])
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
