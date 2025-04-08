<?php

namespace App\Admin\Controllers;

use App\Models\Article;
use App\Repositories\ArticleCategoryRepository;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;
use Illuminate\Support\Str;

class ArticleController extends AdminController
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
        return Grid::make(new Article(['categories']), function (Grid $grid) {

            $grid->column('id')->sortable();
            $grid->column('available', 'Availability')->switch();
            $grid->column('title');
            $grid->column('categories')->pluck('title')->label();

            $grid->column('created_at')->sortable();
            $grid->column('updated_at')->sortable();

            $grid->column('sort')->editable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('title')->width(3);
                $filter->like('content')->width(3);

            });
        });
    }

    /**
     * Make a show builder.
     */
    protected function detail(mixed $id): Show
    {
        return Show::make($id, new Article(), function (Show $show) {
            $show->field('id');
            $show->field('title');
            $show->field('slug');
            $show->field('keywords');
            $show->field('description');
            $show->field('setting');
            $show->field('content');
            $show->field('sort');
            $show->field('available');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     */
    protected function form(): Form
    {
        return Form::make(new Article(['categories']), function (Form $form) {
            $form->defaultEditingChecked();

            $form->tab('Basic', function (Form $form) {
                $form->display('id');

                $availableCategories = $this->articleCategoryRepository->getAvailableTreeList();
                $form->multipleSelect('categories', 'Categories')->required()->options($availableCategories)
                    ->customFormat(function ($v) {
                        if (! $v) {
                            return [];
                        }

                        return array_column($v, 'id');
                    });

                $form->text('title')->required();
                $form->text('slug')->saving(function ($value) use ($form) {
                    return $value ?? Str::slug($form->model()->title);
                });

                $form->markdown('content')->required();
                $form->switch('available');

            })->tab('Optional', function (Form $form) {
                $form->image('cover');
                $form->textarea('keywords')->rows(2);
                $form->textarea('description')->rows(2);
                $form->keyValue('setting')->default([])->setKeyLabel('Key')->setValueLabel('Value')->saveAsJson();

                $form->display('created_at');
                $form->display('updated_at');
            });
        });
    }
}
