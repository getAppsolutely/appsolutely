<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\PageBlockGroupRepository;
use App\Repositories\PageBlockRepository;
use App\Repositories\PageBlockSettingRepository;

final class PageBlockService
{
    public function __construct(
        protected PageBlockGroupRepository $groupRepository,
        protected PageBlockRepository $blockRepository,
        protected PageBlockSettingRepository $settingRepository
    ) {}

    public function getCategorisedBlocks()
    {
        return $this->groupRepository->getCategorisedBlocks();
    }
}
