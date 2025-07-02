<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\FormEntry;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

final class FormEntryRepository extends BaseRepository
{
    public function model(): string
    {
        return FormEntry::class;
    }

    /**
     * Get entries for a specific form
     */
    public function getEntriesByForm(int $formId, bool $includeSpam = true): Collection
    {
        $query = $this->model->newQuery()
            ->where('form_id', $formId)
            ->with(['form', 'user'])
            ->orderBy('submitted_at', 'desc');

        if (! $includeSpam) {
            $query->where('is_spam', false);
        }

        return $query->get();
    }

    /**
     * Get paginated entries for a form
     */
    public function getPaginatedEntriesByForm(int $formId, int $perPage = 15, bool $includeSpam = true): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->where('form_id', $formId)
            ->with(['form', 'user'])
            ->orderBy('submitted_at', 'desc');

        if (! $includeSpam) {
            $query->where('is_spam', false);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get recent entries across all forms
     */
    public function getRecentEntries(int $limit = 10): Collection
    {
        return $this->model->newQuery()
            ->with(['form', 'user'])
            ->where('is_spam', false)
            ->orderBy('submitted_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get entries by user
     */
    public function getEntriesByUser(int $userId): Collection
    {
        return $this->model->newQuery()
            ->where('user_id', $userId)
            ->with(['form'])
            ->orderBy('submitted_at', 'desc')
            ->get();
    }

    /**
     * Get entries by email
     */
    public function getEntriesByEmail(string $email): Collection
    {
        return $this->model->newQuery()
            ->where('email', $email)
            ->with(['form'])
            ->orderBy('submitted_at', 'desc')
            ->get();
    }

    /**
     * Mark entries as spam
     */
    public function markAsSpam(array $entryIds): int
    {
        return $this->model->newQuery()
            ->whereIn('id', $entryIds)
            ->update(['is_spam' => true]);
    }

    /**
     * Mark entries as not spam
     */
    public function markAsNotSpam(array $entryIds): int
    {
        return $this->model->newQuery()
            ->whereIn('id', $entryIds)
            ->update(['is_spam' => false]);
    }

    /**
     * Get spam entries
     */
    public function getSpamEntries(?int $formId = null): Collection
    {
        $query = $this->model->newQuery()
            ->where('is_spam', true)
            ->with(['form', 'user'])
            ->orderBy('submitted_at', 'desc');

        if ($formId) {
            $query->where('form_id', $formId);
        }

        return $query->get();
    }

    /**
     * Create entry with automatic spam detection
     */
    public function createEntryWithSpamCheck(array $data): FormEntry
    {
        // Basic spam detection can be implemented here
        $data['is_spam']      = $this->detectSpam($data);
        $data['submitted_at'] = now();

        return $this->create($data);
    }

    /**
     * Get statistics for a form
     */
    public function getFormStats(int $formId): array
    {
        $total = $this->model->newQuery()
            ->where('form_id', $formId)
            ->count();

        $valid = $this->model->newQuery()
            ->where('form_id', $formId)
            ->where('is_spam', false)
            ->count();

        $spam = $total - $valid;

        $today = $this->model->newQuery()
            ->where('form_id', $formId)
            ->whereDate('submitted_at', today())
            ->where('is_spam', false)
            ->count();

        $thisWeek = $this->model->newQuery()
            ->where('form_id', $formId)
            ->whereBetween('submitted_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('is_spam', false)
            ->count();

        return [
            'total'     => $total,
            'valid'     => $valid,
            'spam'      => $spam,
            'today'     => $today,
            'this_week' => $thisWeek,
        ];
    }

    /**
     * Basic spam detection
     */
    private function detectSpam(array $data): bool
    {
        // Basic spam detection logic
        // This can be enhanced with more sophisticated detection

        // Check for common spam patterns
        $spamKeywords = ['viagra', 'casino', 'lottery', 'prize', 'winner'];
        $content      = implode(' ', array_filter([
            $data['first_name'] ?? '',
            $data['last_name'] ?? '',
            $data['email'] ?? '',
            is_array($data['data']) ? implode(' ', array_values($data['data'])) : '',
        ]));

        $content = strtolower($content);

        foreach ($spamKeywords as $keyword) {
            if (str_contains($content, $keyword)) {
                return true;
            }
        }

        // Check for suspicious patterns
        if (isset($data['email']) && ! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }
}
