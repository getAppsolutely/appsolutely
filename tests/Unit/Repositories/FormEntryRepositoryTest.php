<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Form;
use App\Models\FormEntry;
use App\Models\NotificationQueue;
use App\Repositories\FormEntryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class FormEntryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private FormEntryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(FormEntryRepository::class);
    }

    public function test_get_entries_without_notifications_returns_correct_entries(): void
    {
        $form = Form::factory()->create();

        // Entry with notifications
        $entryWithNotification = FormEntry::factory()->create(['form_id' => $form->id]);
        NotificationQueue::factory()->create(['form_entry_id' => $entryWithNotification->id]);

        // Entry without notifications
        $entryWithoutNotification = FormEntry::factory()->create(['form_id' => $form->id]);

        $result = $this->repository->getEntriesWithoutNotifications($form->id);

        $this->assertCount(1, $result);
        $this->assertTrue($result->contains('id', $entryWithoutNotification->id));
        $this->assertFalse($result->contains('id', $entryWithNotification->id));
    }

    public function test_get_entries_without_notifications_respects_limit(): void
    {
        $form = Form::factory()->create();

        // Create 5 entries without notifications
        FormEntry::factory()->count(5)->create(['form_id' => $form->id]);

        $result = $this->repository->getEntriesWithoutNotifications($form->id, 3);

        $this->assertCount(3, $result);
    }

    public function test_get_entries_without_notifications_returns_all_forms_when_no_form_id(): void
    {
        $form1 = Form::factory()->create();
        $form2 = Form::factory()->create();

        FormEntry::factory()->create(['form_id' => $form1->id]);
        FormEntry::factory()->create(['form_id' => $form2->id]);

        $result = $this->repository->getEntriesWithoutNotifications();

        $this->assertCount(2, $result);
    }

    public function test_has_notifications_returns_true_when_notifications_exist(): void
    {
        $entry = FormEntry::factory()->create();
        NotificationQueue::factory()->create(['form_entry_id' => $entry->id]);

        $result = $this->repository->hasNotifications($entry->id);

        $this->assertTrue($result);
    }

    public function test_has_notifications_returns_false_when_no_notifications_exist(): void
    {
        $entry = FormEntry::factory()->create();

        $result = $this->repository->hasNotifications($entry->id);

        $this->assertFalse($result);
    }

    public function test_get_entries_with_notifications_count_returns_correct_counts(): void
    {
        $form = Form::factory()->create();

        $entry1 = FormEntry::factory()->create(['form_id' => $form->id, 'name' => 'Entry 1']);
        NotificationQueue::factory()->count(3)->create(['form_entry_id' => $entry1->id]);

        $entry2 = FormEntry::factory()->create(['form_id' => $form->id, 'name' => 'Entry 2']);
        NotificationQueue::factory()->count(2)->create(['form_entry_id' => $entry2->id]);

        $result = $this->repository->getEntriesWithNotificationsCount($form->id);

        $this->assertCount(2, $result);

        $firstEntry = $result->firstWhere('id', $entry1->id);
        $this->assertEquals(3, $firstEntry->notifications_count);

        $secondEntry = $result->firstWhere('id', $entry2->id);
        $this->assertEquals(2, $secondEntry->notifications_count);
    }

    public function test_get_by_ids_returns_correct_entries(): void
    {
        $form = Form::factory()->create();

        $entry1 = FormEntry::factory()->create(['form_id' => $form->id]);
        $entry2 = FormEntry::factory()->create(['form_id' => $form->id]);
        $entry3 = FormEntry::factory()->create(['form_id' => $form->id]);

        $result = $this->repository->getByIds([$entry1->id, $entry3->id]);

        $this->assertCount(2, $result);
        $this->assertTrue($result->contains('id', $entry1->id));
        $this->assertTrue($result->contains('id', $entry3->id));
        $this->assertFalse($result->contains('id', $entry2->id));
    }

    public function test_get_by_ids_returns_empty_collection_for_empty_array(): void
    {
        $result = $this->repository->getByIds([]);

        $this->assertCount(0, $result);
    }

    public function test_get_entries_by_filters_paginated_filters_by_form_id(): void
    {
        $form1 = Form::factory()->create();
        $form2 = Form::factory()->create();

        FormEntry::factory()->create(['form_id' => $form1->id]);
        FormEntry::factory()->create(['form_id' => $form1->id]);
        FormEntry::factory()->create(['form_id' => $form2->id]);

        $paginator = $this->repository->getEntriesByFiltersPaginated(['form_id' => $form1->id, 'per_page' => 100]);
        $result    = collect($paginator->items());

        $this->assertCount(2, $result);
        $this->assertTrue($result->every(fn ($entry) => $entry->form_id === $form1->id));
    }

    public function test_get_entries_by_filters_paginated_filters_by_form_slug(): void
    {
        $form = Form::factory()->create(['slug' => 'contact-form']);

        FormEntry::factory()->count(2)->create(['form_id' => $form->id]);

        $paginator = $this->repository->getEntriesByFiltersPaginated(['form_slug' => 'contact-form', 'per_page' => 100]);
        $result    = collect($paginator->items());

        $this->assertCount(2, $result);
    }

    public function test_get_entries_by_filters_paginated_filters_by_entry_id_range(): void
    {
        $form = Form::factory()->create();

        $entry1 = FormEntry::factory()->create(['form_id' => $form->id]);
        $entry2 = FormEntry::factory()->create(['form_id' => $form->id]);
        $entry3 = FormEntry::factory()->create(['form_id' => $form->id]);
        $entry4 = FormEntry::factory()->create(['form_id' => $form->id]);

        $paginator = $this->repository->getEntriesByFiltersPaginated([
            'form_id'       => $form->id,
            'entry_id_from' => (string) $entry2->id,
            'per_page'      => 100,
        ]);
        $result = collect($paginator->items());
        $this->assertGreaterThanOrEqual(3, $result->count());
        $this->assertFalse($result->contains('id', $entry1->id));

        $paginator = $this->repository->getEntriesByFiltersPaginated([
            'form_id'     => $form->id,
            'entry_id_to' => (string) $entry3->id,
            'per_page'    => 100,
        ]);
        $result = collect($paginator->items());
        $this->assertLessThanOrEqual(3, $result->count());
        $this->assertFalse($result->contains('id', $entry4->id));

        $paginator = $this->repository->getEntriesByFiltersPaginated([
            'form_id'       => $form->id,
            'entry_id_from' => (string) $entry2->id,
            'entry_id_to'   => (string) $entry3->id,
            'per_page'      => 100,
        ]);
        $result = collect($paginator->items());
        $this->assertCount(2, $result);
        $this->assertTrue($result->contains('id', $entry2->id));
        $this->assertTrue($result->contains('id', $entry3->id));
    }

    public function test_get_entries_by_filters_paginated_filters_by_date_range(): void
    {
        $form = Form::factory()->create();

        $oldEntry    = FormEntry::factory()->create([
            'form_id'      => $form->id,
            'submitted_at' => now()->subDays(10),
        ]);
        $recentEntry = FormEntry::factory()->create([
            'form_id'      => $form->id,
            'submitted_at' => now()->subDays(2),
        ]);

        $paginator = $this->repository->getEntriesByFiltersPaginated([
            'form_id'   => $form->id,
            'from_date' => now()->subDays(5)->format('Y-m-d'),
            'per_page'  => 100,
        ]);
        $result = collect($paginator->items());
        $this->assertCount(1, $result);
        $this->assertTrue($result->contains('id', $recentEntry->id));
        $this->assertFalse($result->contains('id', $oldEntry->id));
    }

    public function test_get_entries_by_filters_paginated_filters_by_trigger_reference(): void
    {
        $form1 = Form::factory()->create(['slug' => 'contact-form']);
        $form2 = Form::factory()->create(['slug' => 'newsletter-form']);

        FormEntry::factory()->create(['form_id' => $form1->id]);
        FormEntry::factory()->create(['form_id' => $form2->id]);

        $paginator = $this->repository->getEntriesByFiltersPaginated([
            'trigger_reference' => 'contact-form',
            'per_page'          => 100,
        ]);
        $result = collect($paginator->items());
        $this->assertCount(1, $result);
        $this->assertEquals($form1->id, $result->first()->form_id);
    }

    public function test_get_entries_by_filters_paginated_handles_wildcard_trigger_reference(): void
    {
        $form1 = Form::factory()->create();
        $form2 = Form::factory()->create();

        FormEntry::factory()->create(['form_id' => $form1->id]);
        FormEntry::factory()->create(['form_id' => $form2->id]);

        $paginator = $this->repository->getEntriesByFiltersPaginated([
            'trigger_reference' => '*',
            'per_page'          => 100,
        ]);
        $result = collect($paginator->items());
        $this->assertCount(2, $result);
    }

    public function test_get_entries_by_filters_paginated_combines_multiple_filters(): void
    {
        $form = Form::factory()->create(['slug' => 'contact-form']);

        $entry1 = FormEntry::factory()->create([
            'form_id'    => $form->id,
            'created_at' => now()->subDays(5),
        ]);
        $entry2 = FormEntry::factory()->create([
            'form_id'    => $form->id,
            'created_at' => now()->subDays(2),
        ]);
        $entry3 = FormEntry::factory()->create([
            'form_id'    => $form->id,
            'created_at' => now()->subDays(1),
        ]);

        $paginator = $this->repository->getEntriesByFiltersPaginated([
            'form_slug'     => 'contact-form',
            'from_date'     => now()->subDays(3)->format('Y-m-d'),
            'entry_id_from' => (string) $entry2->id,
            'entry_id_to'   => (string) $entry2->id,
            'per_page'      => 100,
        ]);
        $result = collect($paginator->items());
        $this->assertCount(1, $result);
        $this->assertTrue($result->contains('id', $entry2->id));
    }
}
