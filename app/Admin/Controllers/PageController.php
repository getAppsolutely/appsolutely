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
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('name', __t('Name'));
            $grid->column('slug', __t('Slug'));
            $grid->column('title', __t('Title'));
            $grid->column('published_at', __t('Published At'))->display(column_time_format())->sortable();
            $grid->column('expired_at', __t('Expired At'))->display(column_time_format())->sortable();
            $grid->column('status', __t('Status'))->switch();
            $grid->column('created_at', __t('Created At'))->display(column_time_format());
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
            $form->display('id', __t('ID'));
            $form->text('name', __t('Name'))->required();
            $form->text('slug', __t('Slug'))->required();
            $form->text('title', __t('Title'))->required();
            $form->textarea('description', __t('Description'))->rows(3);
            $form->text('keywords', __t('Keywords'));
            $form->textarea('content', __t('Content'))->rows(6);
            $form->text('canonical_url', __t('Canonical URL'));
            $form->text('meta_robots', __t('Meta Robots'));
            $form->text('og_title', __t('OG Title'));
            $form->textarea('og_description', __t('OG Description'))->rows(3);
            $form->text('og_image', __t('OG Image'));
            $form->keyValue('structured_data', __t('Structured Data'))->default([])->setKeyLabel('Key')->setValueLabel('Value')->saveAsJson();
            $form->text('hreflang', __t('Hreflang'));
            $form->text('language', __t('Language'));

            $form->datetime('published_at', __t('Published At (%s)', [app_local_timezone()]));
            $form->datetime('expired_at', __t('Expired At (%s)', [app_local_timezone()]));
            $form->switch('status', __t('Status'));

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
