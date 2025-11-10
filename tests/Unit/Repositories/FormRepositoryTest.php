<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Form;
use App\Models\FormField;
use App\Repositories\FormRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class FormRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private FormRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(FormRepository::class);
    }

    public function test_find_by_slug_returns_active_form_with_fields(): void
    {
        $form = Form::factory()->create(['slug' => 'contact-form', 'status' => 1]);
        FormField::factory()->create(['form_id' => $form->id, 'sort' => 1]);
        FormField::factory()->create(['form_id' => $form->id, 'sort' => 2]);

        $result = $this->repository->findBySlug('contact-form');

        $this->assertInstanceOf(Form::class, $result);
        $this->assertEquals($form->id, $result->id);
        $this->assertTrue($result->relationLoaded('fields'));
        $this->assertCount(2, $result->fields);
    }

    public function test_find_by_slug_returns_null_for_inactive_form(): void
    {
        Form::factory()->create(['slug' => 'contact-form', 'status' => 0]);

        $result = $this->repository->findBySlug('contact-form');

        $this->assertNull($result);
    }

    public function test_get_active_forms_with_fields_returns_only_active_forms(): void
    {
        $active1 = Form::factory()->create(['status' => 1, 'name' => 'Form A']);
        $active2 = Form::factory()->create(['status' => 1, 'name' => 'Form B']);
        Form::factory()->create(['status' => 0]);

        FormField::factory()->create(['form_id' => $active1->id, 'sort' => 1]);
        FormField::factory()->create(['form_id' => $active2->id, 'sort' => 1]);

        $result = $this->repository->getActiveFormsWithFields();

        $this->assertCount(2, $result);
        $this->assertTrue($result->contains('id', $active1->id));
        $this->assertTrue($result->contains('id', $active2->id));
    }

    public function test_get_form_with_stats_includes_entry_counts(): void
    {
        $form = Form::factory()->create();
        FormField::factory()->create(['form_id' => $form->id]);

        $result = $this->repository->getFormWithStats($form->id);

        $this->assertInstanceOf(Form::class, $result);
        $this->assertArrayHasKey('entries_count', $result->getAttributes());
        $this->assertArrayHasKey('valid_entries_count', $result->getAttributes());
    }

    public function test_create_with_fields_creates_form_and_fields(): void
    {
        $formData   = ['name' => 'Test Form', 'slug' => 'test-form', 'status' => 1];
        $fieldsData = [
            ['name' => 'Name', 'type' => 'text', 'sort' => 1],
            ['name' => 'Email', 'type' => 'email', 'sort' => 2],
        ];

        $result = $this->repository->createWithFields($formData, $fieldsData);

        $this->assertInstanceOf(Form::class, $result);
        $this->assertEquals('Test Form', $result->name);
        $this->assertCount(2, $result->fields);
        $this->assertTrue($result->relationLoaded('fields'));
    }

    public function test_update_with_fields_updates_form_and_syncs_fields(): void
    {
        $form   = Form::factory()->create();
        $field1 = FormField::factory()->create(['form_id' => $form->id]);
        $field2 = FormField::factory()->create(['form_id' => $form->id]);

        $formData   = ['name' => 'Updated Form'];
        $fieldsData = [
            ['id' => $field1->id, 'name' => 'Updated Field 1', 'sort' => 1],
            ['name' => 'New Field', 'type' => 'text', 'sort' => 2],
        ];

        $result = $this->repository->updateWithFields($form->id, $formData, $fieldsData);

        $this->assertEquals('Updated Form', $result->name);
        $this->assertCount(2, $result->fields);
        $this->assertDatabaseMissing('form_fields', ['id' => $field2->id]);
    }

    public function test_count_by_status_returns_correct_count(): void
    {
        Form::factory()->count(3)->create(['status' => 1]);
        Form::factory()->count(2)->create(['status' => 0]);

        $this->assertEquals(3, $this->repository->countByStatus(1));
        $this->assertEquals(2, $this->repository->countByStatus(0));
    }
}
