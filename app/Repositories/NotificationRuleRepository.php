<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\NotificationRule;
use Illuminate\Database\Eloquent\Collection;

final class NotificationRuleRepository extends BaseRepository
{
    public function model(): string
    {
        return NotificationRule::class;
    }

    /**
     * Find rules for specific trigger
     */
    public function findByTrigger(string $triggerType, string $reference): Collection
    {
        return $this->model->newQuery()->status()
            ->where('trigger_type', $triggerType)
            ->where('trigger_reference', $reference)
            ->with('template')
            ->get();
    }

    /**
     * Get all active rules with templates
     */
    public function getActiveWithTemplates(): Collection
    {
        return $this->model->newQuery()->status()->with('template')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get rules by trigger type
     */
    public function getByTriggerType(string $triggerType): Collection
    {
        return $this->model->newQuery()->where('trigger_type', $triggerType)
            ->with('template')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get rules by template
     */
    public function getByTemplate(int $templateId): Collection
    {
        return $this->model->newQuery()->where('template_id', $templateId)
            ->orderBy('name')
            ->get();
    }

    /**
     * Create rule with validation
     */
    public function createRule(array $data): NotificationRule
    {
        // Ensure proper data types for JSON fields
        $data = $this->getArr($data);

        return $this->create($data);
    }

    /**
     * Update rule with validation
     */
    public function updateRule(int $id, array $data): NotificationRule
    {
        // Ensure proper data types for JSON fields
        $data = $this->getArr($data);

        $this->update($id, $data);

        return $this->find($id);
    }

    /**
     * Get rules with usage statistics
     */
    public function getWithUsageStats(): Collection
    {
        return $this->model->newQuery()->withCount([
            'queueItems as total_sent' => function ($query) {
                $query->where('status', 'sent');
            },
            'queueItems as pending_count' => function ($query) {
                $query->where('status', 'pending');
            },
            'queueItems as failed_count' => function ($query) {
                $query->where('status', 'failed');
            },
        ])->with('template')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get available trigger types with counts
     */
    public function getTriggerTypesWithCounts(): array
    {
        return $this->model->newQuery()->selectRaw('trigger_type, COUNT(*) as count')
            ->where('status', 1)
            ->groupBy('trigger_type')
            ->pluck('count', 'trigger_type')
            ->toArray();
    }

    /**
     * Get rules that need template
     */
    public function getRulesNeedingTemplate(): Collection
    {
        return $this->model->newQuery()->whereDoesntHave('template')->get();
    }

    /**
     * Duplicate rule
     */
    public function duplicate(int $id): NotificationRule
    {
        $rule = $this->find($id);
        $data = $rule->toArray();

        unset($data['id'], $data['created_at'], $data['updated_at']);

        $data['name']   = $data['name'] . ' (Copy)';
        $data['status'] = 0; // Create as inactive by default

        return $this->create($data);
    }

    /**
     * Get rules by recipient type
     */
    public function getByRecipientType(string $recipientType): Collection
    {
        return $this->model->newQuery()->where('recipient_type', $recipientType)
            ->with('template')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get conditional rules for specific field
     */
    public function getConditionalRulesForField(string $fieldName): Collection
    {
        return $this->model->newQuery()->where('recipient_type', 'conditional')
            ->whereJsonContains('conditions->field', $fieldName)
            ->with('template')
            ->get();
    }

    /**
     * Get rules with delayed sending
     */
    public function getDelayedRules(): Collection
    {
        return $this->model->newQuery()->where('delay_minutes', '>', 0)
            ->with('template')
            ->orderBy('delay_minutes')
            ->get();
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(array $ids, int $status): int
    {
        return $this->model->newQuery()->whereIn('id', $ids)->update(['status' => $status]);
    }

    public function getArr(array $data): array
    {
        if (isset($data['recipient_emails']) && is_string($data['recipient_emails'])) {
            $data['recipient_emails'] = array_filter(array_map('trim', explode(',', $data['recipient_emails'])));
        }

        if (isset($data['conditions']) && is_string($data['conditions'])) {
            $data['conditions'] = json_decode($data['conditions'], true);
        }

        return $data;
    }
}
