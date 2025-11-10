<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Constants\BasicConstant;
use App\Models\Page;
use App\Repositories\PageBlockSettingRepository;
use App\Repositories\PageRepository;
use App\Services\Contracts\PageBlockSettingServiceInterface;
use App\Services\Contracts\PageStructureServiceInterface;
use App\Services\PageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Mockery;
use Tests\TestCase;

final class PageServiceTest extends TestCase
{
    private PageRepository $pageRepository;

    private PageBlockSettingRepository $pageBlockSettingRepository;

    private PageBlockSettingServiceInterface $blockSettingService;

    private PageStructureServiceInterface $structureService;

    private PageService $pageService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pageRepository             = Mockery::mock(PageRepository::class);
        $this->pageBlockSettingRepository = Mockery::mock(PageBlockSettingRepository::class);
        $this->blockSettingService        = Mockery::mock(PageBlockSettingServiceInterface::class);
        $this->structureService           = Mockery::mock(PageStructureServiceInterface::class);

        $this->pageService = new PageService(
            $this->pageRepository,
            $this->pageBlockSettingRepository,
            $this->blockSettingService,
            $this->structureService
        );
    }

    public function test_find_published_page_by_slug_returns_page(): void
    {
        $slug = 'test-page';
        $page = new Page(['slug' => $slug, 'title' => 'Test Page']);

        $this->pageRepository
            ->shouldReceive('findPageBySlug')
            ->once()
            ->with($slug, Mockery::type(Carbon::class))
            ->andReturn($page);

        $result = $this->pageService->findPublishedPage($slug);

        $this->assertInstanceOf(Page::class, $result);
        $this->assertEquals($slug, $result->slug);
    }

    public function test_find_published_page_by_slug_returns_null_when_not_found(): void
    {
        $slug = 'non-existent';

        $this->pageRepository
            ->shouldReceive('findPageBySlug')
            ->once()
            ->with($slug, Mockery::type(Carbon::class))
            ->andReturn(null);

        $result = $this->pageService->findPublishedPage($slug);

        $this->assertNull($result);
    }

    public function test_find_published_page_by_id_returns_page(): void
    {
        $id   = 1;
        $page = new Page(['id' => $id, 'title' => 'Test Page']);

        $this->pageRepository
            ->shouldReceive('findPageById')
            ->once()
            ->with($id, Mockery::type(Carbon::class))
            ->andReturn($page);

        $result = $this->pageService->findPublishedPageById($id);

        $this->assertInstanceOf(Page::class, $result);
        $this->assertEquals($id, $result->id);
    }

    public function test_find_published_page_by_id_returns_null_when_not_found(): void
    {
        $id = 999;

        $this->pageRepository
            ->shouldReceive('findPageById')
            ->once()
            ->with($id, Mockery::type(Carbon::class))
            ->andReturn(null);

        $result = $this->pageService->findPublishedPageById($id);

        $this->assertNull($result);
    }

    public function test_find_by_reference_returns_page(): void
    {
        $reference = 'test-reference';
        $page      = new Page(['reference' => $reference, 'title' => 'Test Page']);

        $queryBuilder = Mockery::mock();
        $queryBuilder->shouldReceive('reference')
            ->once()
            ->with($reference)
            ->andReturnSelf();
        $queryBuilder->shouldReceive('firstOrFail')
            ->once()
            ->andReturn($page);

        $this->pageRepository
            ->shouldReceive('with')
            ->once()
            ->with(['blocks'])
            ->andReturn($queryBuilder);

        $result = $this->pageService->findByReference($reference);

        $this->assertInstanceOf(Model::class, $result);
        $this->assertEquals($reference, $result->reference);
    }

    public function test_find_by_reference_throws_exception_when_not_found(): void
    {
        $reference = 'non-existent';

        $queryBuilder = Mockery::mock();
        $queryBuilder->shouldReceive('reference')
            ->once()
            ->with($reference)
            ->andReturnSelf();
        $queryBuilder->shouldReceive('firstOrFail')
            ->once()
            ->andThrow(new \Illuminate\Database\Eloquent\ModelNotFoundException());

        $this->pageRepository
            ->shouldReceive('with')
            ->once()
            ->with(['blocks'])
            ->andReturn($queryBuilder);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->pageService->findByReference($reference);
    }

    public function test_reset_setting_resets_page_and_block_settings(): void
    {
        $reference = 'test-reference';
        $page      = new Page(['id' => 1, 'reference' => $reference]);

        $queryBuilder = Mockery::mock();
        $queryBuilder->shouldReceive('reference')
            ->once()
            ->with($reference)
            ->andReturnSelf();
        $queryBuilder->shouldReceive('firstOrFail')
            ->once()
            ->andReturn($page);

        $this->pageRepository
            ->shouldReceive('with')
            ->once()
            ->with(['blocks'])
            ->andReturn($queryBuilder);

        $this->pageRepository
            ->shouldReceive('updateSetting')
            ->once()
            ->with($page->id, [])
            ->andReturn($page);

        $this->pageBlockSettingRepository
            ->shouldReceive('resetSetting')
            ->once()
            ->with($page->id)
            ->andReturn(1);

        $this->pageRepository
            ->shouldReceive('find')
            ->once()
            ->with($page->id)
            ->andReturn($page);

        $result = $this->pageService->resetSetting($reference);

        $this->assertInstanceOf(Model::class, $result);
    }

    public function test_save_setting_saves_page_and_syncs_block_settings(): void
    {
        $reference = 'test-reference';
        $page      = new Page(['id' => 1, 'reference' => $reference]);
        $data      = [
            'title'                          => 'Updated Title',
            BasicConstant::PAGE_GRAPESJS_KEY => [
                ['id' => 'block-1', 'type' => 'text'],
            ],
        ];

        $queryBuilder = Mockery::mock();
        $queryBuilder->shouldReceive('reference')
            ->once()
            ->with($reference)
            ->andReturnSelf();
        $queryBuilder->shouldReceive('firstOrFail')
            ->once()
            ->andReturn($page);

        $this->pageRepository
            ->shouldReceive('with')
            ->once()
            ->with(['blocks'])
            ->andReturn($queryBuilder);

        $this->pageBlockSettingRepository
            ->shouldReceive('resetSetting')
            ->once()
            ->with($page->id)
            ->andReturn(1);

        $this->blockSettingService
            ->shouldReceive('syncSettings')
            ->once()
            ->with([['id' => 'block-1', 'type' => 'text']], $page->id)
            ->andReturn([]);

        $this->pageRepository
            ->shouldReceive('updateSetting')
            ->once()
            ->with($page->id, $data)
            ->andReturn($page);

        $this->pageRepository
            ->shouldReceive('find')
            ->once()
            ->with($page->id)
            ->andReturn($page);

        $result = $this->pageService->saveSetting($reference, $data);

        $this->assertInstanceOf(Model::class, $result);
    }

    public function test_save_setting_handles_empty_block_data(): void
    {
        $reference = 'test-reference';
        $page      = new Page(['id' => 1, 'reference' => $reference]);
        $data      = ['title' => 'Updated Title'];

        $queryBuilder = Mockery::mock();
        $queryBuilder->shouldReceive('reference')
            ->once()
            ->with($reference)
            ->andReturnSelf();
        $queryBuilder->shouldReceive('firstOrFail')
            ->once()
            ->andReturn($page);

        $this->pageRepository
            ->shouldReceive('with')
            ->once()
            ->with(['blocks'])
            ->andReturn($queryBuilder);

        $this->pageBlockSettingRepository
            ->shouldReceive('resetSetting')
            ->once()
            ->with($page->id)
            ->andReturn(1);

        $this->blockSettingService
            ->shouldReceive('syncSettings')
            ->once()
            ->with([], $page->id)
            ->andReturn([]);

        $this->pageRepository
            ->shouldReceive('updateSetting')
            ->once()
            ->with($page->id, $data)
            ->andReturn($page);

        $this->pageRepository
            ->shouldReceive('find')
            ->once()
            ->with($page->id)
            ->andReturn($page);

        $result = $this->pageService->saveSetting($reference, $data);

        $this->assertInstanceOf(Model::class, $result);
    }

    public function test_save_setting_handles_non_array_block_data(): void
    {
        $reference = 'test-reference';
        $page      = new Page(['id' => 1, 'reference' => $reference]);
        $data      = [
            'title'                          => 'Updated Title',
            BasicConstant::PAGE_GRAPESJS_KEY => 'not-an-array',
        ];

        $queryBuilder = Mockery::mock();
        $queryBuilder->shouldReceive('reference')
            ->once()
            ->with($reference)
            ->andReturnSelf();
        $queryBuilder->shouldReceive('firstOrFail')
            ->once()
            ->andReturn($page);

        $this->pageRepository
            ->shouldReceive('with')
            ->once()
            ->with(['blocks'])
            ->andReturn($queryBuilder);

        $this->pageBlockSettingRepository
            ->shouldReceive('resetSetting')
            ->once()
            ->with($page->id)
            ->andReturn(1);

        $this->blockSettingService
            ->shouldReceive('syncSettings')
            ->once()
            ->with([], $page->id)
            ->andReturn([]);

        $this->pageRepository
            ->shouldReceive('updateSetting')
            ->once()
            ->with($page->id, $data)
            ->andReturn($page);

        $this->pageRepository
            ->shouldReceive('find')
            ->once()
            ->with($page->id)
            ->andReturn($page);

        $result = $this->pageService->saveSetting($reference, $data);

        $this->assertInstanceOf(Model::class, $result);
    }

    public function test_sync_settings_delegates_to_block_setting_service(): void
    {
        $data     = [['id' => 'block-1']];
        $pageId   = 1;
        $expected = ['result' => 'synced'];

        $this->blockSettingService
            ->shouldReceive('syncSettings')
            ->once()
            ->with($data, $pageId)
            ->andReturn($expected);

        $result = $this->pageService->syncSettings($data, $pageId);

        $this->assertEquals($expected, $result);
    }

    public function test_get_block_value_id_delegates_to_block_setting_service(): void
    {
        $blockId  = 5;
        $expected = 10;

        $this->blockSettingService
            ->shouldReceive('getBlockValueId')
            ->once()
            ->with($blockId)
            ->andReturn($expected);

        $result = $this->pageService->getBlockValueId($blockId);

        $this->assertEquals($expected, $result);
    }

    public function test_generate_default_page_setting_delegates_to_structure_service(): void
    {
        $expected = ['pages' => [['frames' => []]]];

        $this->structureService
            ->shouldReceive('generateDefaultPageSetting')
            ->once()
            ->andReturn($expected);

        $result = $this->pageService->generateDefaultPageSetting();

        $this->assertEquals($expected, $result);
    }
}
