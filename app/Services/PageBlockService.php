<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PageBlock;
use App\Models\PageBlockGroup;
use App\Models\PageBlockSetting;
use App\Repositories\PageBlockGroupRepository;
use App\Repositories\PageBlockRepository;
use App\Repositories\PageBlockSettingRepository;

final class PageBlockService
{
    public function __construct(
        private PageBlockGroupRepository $groupRepository,
        private PageBlockRepository $blockRepository,
        private PageBlockSettingRepository $settingRepository
    ) {}

    // PageBlockGroup CRUD
    public function getAllGroups(): iterable
    {
        return $this->groupRepository->all();
    }

    public function createGroup(array $data): PageBlockGroup
    {
        return $this->groupRepository->create($data);
    }

    public function updateGroup(int $id, array $data): bool
    {
        return $this->groupRepository->update($id, $data);
    }

    public function deleteGroup(int $id): bool
    {
        return $this->groupRepository->delete($id);
    }

    // PageBlock CRUD
    public function getAllBlocks(): iterable
    {
        return $this->blockRepository->all();
    }

    public function createBlock(array $data): PageBlock
    {
        return $this->blockRepository->create($data);
    }

    public function updateBlock(int $id, array $data): bool
    {
        return $this->blockRepository->update($id, $data);
    }

    public function deleteBlock(int $id): bool
    {
        return $this->blockRepository->delete($id);
    }

    // PageBlockSetting CRUD
    public function getAllSettings(): iterable
    {
        return $this->settingRepository->all();
    }

    public function createSetting(array $data): PageBlockSetting
    {
        return $this->settingRepository->create($data);
    }

    public function updateSetting(int $id, array $data): bool
    {
        return $this->settingRepository->update($id, $data);
    }

    public function deleteSetting(int $id): bool
    {
        return $this->settingRepository->delete($id);
    }
}
