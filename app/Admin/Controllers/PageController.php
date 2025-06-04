<?php

namespace App\Admin\Controllers;

use App\Models\Page;
use App\Repositories\PageRepository;
use App\Services\PageService;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

class PageController extends AdminBaseController
{
    public function __construct(protected PageRepository $pageRepository, protected PageService $pageService) {}

    protected function grid(): Grid
    {
        return Grid::make(Page::query(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('slug');
            $grid->column('title');
            $grid->column('published_at')->display(column_time_format())->sortable();
            $grid->column('expired_at')->display(column_time_format())->sortable();
            $grid->column('status')->switch();
            $grid->column('created_at')->display(column_time_format());
            $grid->model()->orderByDesc('id');

            $grid->quickSearch('id', 'name', 'slug', 'title');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(4);
                $filter->like('name')->width(4);
                $filter->like('slug')->width(4);
                $filter->like('title')->width(4);
                $filter->equal('status')->width(4);
                $filter->between('created_at')->datetime()->width(4);
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
                $actions->prepend(admin_link_action('Design', admin_url('pages/' . $actions->getKey() . '/design'), '_blank'));
            });
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

            $form->datetime('published_at', __t('Published At (%s)', [app_local_timezone()]));
            $form->datetime('expired_at', __t('Expired At (%s)', [app_local_timezone()]));
            $form->switch('status');

            $form->disableViewButton();
            $form->disableViewCheck();
        });
    }

    /**
     * Show the page builder interface
     */
    public function design(int $pageId)
    {
        $page = $this->pageRepository->findOrFail($pageId);

        return view('page-builder::index', [
            'page'   => $page,
            'pageId' => $pageId,
        ]);
    }
}
