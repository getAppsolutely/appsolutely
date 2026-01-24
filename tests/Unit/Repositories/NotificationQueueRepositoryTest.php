<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\FormEntry;
use App\Models\NotificationQueue;
use App\Models\NotificationRule;
use App\Repositories\NotificationQueueRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class NotificationQueueRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private NotificationQueueRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(NotificationQueueRepository::class);
    }

    public function test_has_notification_for_entry_and_rule_returns_true_when_exists(): void
    {
        $entry = FormEntry::factory()->create();
        $rule  = NotificationRule::factory()->create();

        NotificationQueue::factory()->create([
            'form_entry_id' => $entry->id,
            'rule_id'       => $rule->id,
        ]);

        $result = $this->repository->hasNotificationForEntryAndRule($entry->id, $rule->id);

        $this->assertTrue($result);
    }

    public function test_has_notification_for_entry_and_rule_returns_false_when_not_exists(): void
    {
        $entry = FormEntry::factory()->create();
        $rule  = NotificationRule::factory()->create();

        $result = $this->repository->hasNotificationForEntryAndRule($entry->id, $rule->id);

        $this->assertFalse($result);
    }

    public function test_has_notification_for_entry_and_rule_checks_specific_combination(): void
    {
        $entry1 = FormEntry::factory()->create();
        $entry2 = FormEntry::factory()->create();
        $rule1  = NotificationRule::factory()->create();
        $rule2  = NotificationRule::factory()->create();

        // Create notification for entry1 + rule1
        NotificationQueue::factory()->create([
            'form_entry_id' => $entry1->id,
            'rule_id'       => $rule1->id,
        ]);

        // Should return true for entry1 + rule1
        $this->assertTrue($this->repository->hasNotificationForEntryAndRule($entry1->id, $rule1->id));

        // Should return false for other combinations
        $this->assertFalse($this->repository->hasNotificationForEntryAndRule($entry1->id, $rule2->id));
        $this->assertFalse($this->repository->hasNotificationForEntryAndRule($entry2->id, $rule1->id));
        $this->assertFalse($this->repository->hasNotificationForEntryAndRule($entry2->id, $rule2->id));
    }

    public function test_count_for_entry_and_rule_returns_correct_count(): void
    {
        $entry = FormEntry::factory()->create();
        $rule  = NotificationRule::factory()->create();

        // Create multiple notifications for same entry and rule
        NotificationQueue::factory()->count(3)->create([
            'form_entry_id' => $entry->id,
            'rule_id'       => $rule->id,
        ]);

        $result = $this->repository->countForEntryAndRule($entry->id, $rule->id);

        $this->assertEquals(3, $result);
    }

    public function test_count_for_entry_and_rule_returns_zero_when_no_notifications(): void
    {
        $entry = FormEntry::factory()->create();
        $rule  = NotificationRule::factory()->create();

        $result = $this->repository->countForEntryAndRule($entry->id, $rule->id);

        $this->assertEquals(0, $result);
    }

    public function test_count_for_entry_and_rule_only_counts_specific_combination(): void
    {
        $entry = FormEntry::factory()->create();
        $rule1 = NotificationRule::factory()->create();
        $rule2 = NotificationRule::factory()->create();

        // Create 2 notifications for rule1
        NotificationQueue::factory()->count(2)->create([
            'form_entry_id' => $entry->id,
            'rule_id'       => $rule1->id,
        ]);

        // Create 3 notifications for rule2
        NotificationQueue::factory()->count(3)->create([
            'form_entry_id' => $entry->id,
            'rule_id'       => $rule2->id,
        ]);

        $this->assertEquals(2, $this->repository->countForEntryAndRule($entry->id, $rule1->id));
        $this->assertEquals(3, $this->repository->countForEntryAndRule($entry->id, $rule2->id));
    }

    public function test_create_queue_item_with_form_entry_id(): void
    {
        $entry    = FormEntry::factory()->create();
        $rule     = NotificationRule::factory()->create();
        $template = \App\Models\NotificationTemplate::factory()->create();

        $queueItem = $this->repository->createQueueItem([
            'rule_id'         => $rule->id,
            'template_id'     => $template->id,
            'form_entry_id'   => $entry->id,
            'recipient_email' => 'test@example.com',
            'subject'         => 'Test Subject',
            'body_html'       => '<p>Test</p>',
            'body_text'       => 'Test',
            'trigger_data'    => ['test' => 'data'],
            'status'          => 'pending',
            'scheduled_at'    => now(),
        ]);

        $this->assertInstanceOf(NotificationQueue::class, $queueItem);
        $this->assertEquals($entry->id, $queueItem->form_entry_id);
        $this->assertEquals($rule->id, $queueItem->rule_id);
        $this->assertEquals('test@example.com', $queueItem->recipient_email);
        $this->assertEquals('pending', $queueItem->status);
    }

    public function test_queue_item_can_be_created_without_form_entry_id(): void
    {
        $rule     = NotificationRule::factory()->create();
        $template = \App\Models\NotificationTemplate::factory()->create();

        $queueItem = $this->repository->createQueueItem([
            'rule_id'         => $rule->id,
            'template_id'     => $template->id,
            'recipient_email' => 'test@example.com',
            'subject'         => 'Test Subject',
            'body_html'       => '<p>Test</p>',
            'body_text'       => 'Test',
            'trigger_data'    => ['test' => 'data'],
            'status'          => 'pending',
            'scheduled_at'    => now(),
        ]);

        $this->assertInstanceOf(NotificationQueue::class, $queueItem);
        $this->assertNull($queueItem->form_entry_id);
    }

    public function test_form_entry_id_is_nullable_on_delete(): void
    {
        $entry = FormEntry::factory()->create();
        $rule  = NotificationRule::factory()->create();

        $queueItem = NotificationQueue::factory()->create([
            'form_entry_id' => $entry->id,
            'rule_id'       => $rule->id,
        ]);

        // Force delete the form entry (not soft delete)
        $entry->forceDelete();

        // Refresh queue item from database
        $queueItem->refresh();

        // form_entry_id should be set to null due to onDelete('set null')
        $this->assertNull($queueItem->form_entry_id);
    }
}
