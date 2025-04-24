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

            $grid->column('id')->sortable();
            $grid->column('status', 'Status')->switchable();
            $grid->column('title');
            $grid->column('categories')->pluck('title')->label();

            $grid->column('published_at')->display(column_time_format())->sortable();
            $grid->column('expired_at')->display(column_time_format())->sortable();
            $grid->column('created_at');

            $grid->column('sort')->quickEdit();

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
                $form->display('id');

                $availableCategories = $this->articleCategoryRepository->getActiveList();
                $form->multipleSelect('categories', 'Categories')->required()->options($availableCategories)
                    ->customFormat(extract_values());

                $form->text('title')->required();
                $form->text('slug');

                $form->markdown('content')->required()->options(Markdown::options())->script(Markdown::script());
                $form->datetime('published_at');
                $form->datetime('expired_at');
                $form->switch('status');

            })->tab('Optional', function (Form $form) {
                $form->image('cover')->autoUpload()->url(upload_url(Article::class, $form->getKey()));
                $form->textarea('keywords')->rows(2);
                $form->textarea('description')->rows(2);
                $form->keyValue('setting')->default([])->setKeyLabel('Key')->setValueLabel('Value')->saveAsJson();

                $form->display('created_at');
                $form->display('updated_at');
            });
        });
    }
}
