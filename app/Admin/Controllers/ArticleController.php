<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\Fields\Markdown;
use App\Models\Article;
use App\Repositories\ArticleCategoryRepository;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

class ArticleController extends AdminBaseController
{
    protected ArticleCategoryRepository $articleCategoryRepository;

    public function __construct(ArticleCategoryRepository $articleCategoryRepository)
    {
        $this->articleCategoryRepository = $articleCategoryRepository;
    }

    /**
     * Make a grid builder.
     */
    protected function grid(): Grid
    {
        return Grid::make(Article::with(['categories']), function (Grid $grid) {

            $grid->column('id', __t('ID'))->sortable();
            $grid->column('status', __t('Status'))->switch();
            $grid->column('title', __t('Title'))->editable();
            $grid->column('categories', __t('Categories'))->pluck('title')->label();

            $grid->column('published_at', __t('Published At'))->display(column_time_format())->sortable();
            $grid->column('expired_at', __t('Expired At'))->display(column_time_format())->sortable();
            $grid->column('created_at', __t('Created At'))->display(column_time_format());
            $grid->column('sort', __t('Sort'))->editable();
            $grid->model()->orderByDesc('id');

            $grid->quickSearch('id', 'title');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('title')->width(3);
                $filter->like('content')->width(3);
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });
        });
    }

    /**
     * Make a form builder.
     */
    protected function form(): Form
    {
        return Form::make(Article::with(['categories']), function (Form $form) {
            $form->defaultEditingChecked();

            $form->tab('Basic', function (Form $form) {
                $form->display('id', __t('ID'));

                $availableCategories = $this->articleCategoryRepository->getActiveList();
                $form->multipleSelect('categories', 'Categories')->required()->options($availableCategories)
                    ->customFormat(extract_values());

                $form->text('title', __t('Title'))->required();
                $form->text('slug', __t('Slug'));

                $form->markdown('content', __t('Content'))->required()->options(Markdown::options())->script(Markdown::script());
                $form->datetime('published_at', __t('Published At'));
                $form->datetime('expired_at', __t('Expired At'));
                $form->switch('status', __t('Status'));

            })->tab('Optional', function (Form $form) {
                $form->image('cover', __t('Cover'))->autoUpload()->url(upload_to_api(Article::class, $form->getKey()));
                $form->textarea('keywords', __t('Keywords'))->rows(2);
                $form->textarea('description', __t('Description'))->rows(2);
                $form->keyValue('setting', __t('Setting'))->default([])->setKeyLabel('Key')->setValueLabel('Value')->saveAsJson();
            });
        });
    }
}
