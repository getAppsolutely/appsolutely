<?php

namespace App\Admin\Controllers;

use App\Models\Page;
use App\Repositories\PageRepository;
use App\Services\PageService;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

class PageController extends AdminBaseController
{
    protected PageRepository $pageRepository;

    protected PageService $pageService;

    public function __construct(PageRepository $pageRepository, PageService $pageService)
    {
        $this->pageRepository = $pageRepository;
        $this->pageService    = $pageService;
    }

    protected function grid(): Grid
    {
        return Grid::make(Page::query(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('slug');
            $grid->column('title');
            $grid->column('published_at')->sortable();
            $grid->column('expired_at')->sortable();
            $grid->column('status')->switch();
            $grid->column('created_at');

            $grid->quickSearch('id', 'name', 'slug', 'title');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('name');
                $filter->like('slug');
                $filter->like('title');
                $filter->equal('status');
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });

            $grid->model()->orderByDesc('id');
        });
    }

    protected function form(): Form
    {
        return Form::make(Page::query(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->text('slug')->required();
            $form->text('title')->required();
            $form->textarea('description')->rows(3);
            $form->text('keywords');
            $form->textarea('content')->rows(6);
            $form->text('canonical_url');
            $form->text('meta_robots');
            $form->text('og_title');
            $form->textarea('og_description')->rows(3);
            $form->text('og_image');
            $form->keyValue('structured_data')->default([])->setKeyLabel('Key')->setValueLabel('Value')->saveAsJson();
            $form->text('hreflang');
            $form->text('language');
            $form->datetime('published_at');
            $form->datetime('expired_at');
            $form->switch('status');
            $form->display('created_at');
            $form->display('updated_at');

            $form->disableViewButton();
            $form->disableViewCheck();
        });
    }
}
