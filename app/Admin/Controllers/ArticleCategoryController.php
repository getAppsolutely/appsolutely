<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\NestedSetForm as Form;
use App\Models\ArticleCategory;
use App\Repositories\ArticleCategoryRepository;
use Dcat\Admin\Form\BlockForm;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Show;
use Dcat\Admin\Tree;
use Illuminate\Support\Facades\Request;

class ArticleCategoryController extends BaseAdminController
{
    public function __construct(protected ArticleCategoryRepository $articleCategoryRepository) {}

    public function index1(Content $content)
    {

        return $content
            ->header('Article Categories')
            ->body(function (Row $row) {
                $tree = new Tree(new ArticleCategory());
                $tree->disableSaveButton();
                $row->column(12, $tree);
            });
    }

    /**
     * Make a grid builder.
     */
    protected function grid(): Grid
    {
        return Grid::make(new ArticleCategory(), function (Grid $grid) {
            $grid->enableDialogCreate();
            $grid->setDialogFormDimensions('85%', '85%');
            $grid->model()->orderBy('left', 'ASC');

            $grid->column('id')->width('50px');
            $grid->column('title')->tree(true)->width('400px');
            $grid->column('status')->switch();
            $grid->column('slug')->textarea()->width('240px');
            $grid->order->orderable();

            $grid->quickSearch('id', 'title');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(3);
                $filter->like('title')->width(3);
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });
        });
    }

    /**
     * Make a show builder.
     */
    protected function detail(mixed $id): Show
    {
        return Show::make($id, new ArticleCategory(), function (Show $show) {
            $show->field('id');
            $show->field('parent_id');
            $show->field('title');
            $show->field('keywords');
            $show->field('description');
            $show->field('slug');
            $show->field('setting');
            $show->field('status');
            $show->field('cover');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     */
    protected function form(): Form
    {
        return Form::make(new ArticleCategory(), function (Form $form) {
            $form->block(7, function (BlockForm $form) {
                $form->title(__('Basic'));

                $form->display('id');

                $availableCategories = $this->articleCategoryRepository->getActiveList();
                $form->select('parent_id', 'Parent')->options($availableCategories);

                $form->text('title')->required();
                $form->text('slug');

                $form->textarea('keywords')->rows(3);
                $form->textarea('description')->rows(5);
                $form->keyValue('setting')->default([])->setKeyLabel('Key')->setValueLabel('Value')->saveAsJson();

            });

            $form->block(5, function (BlockForm $form) {
                $form->title('Optional');

                $form->image('cover')->autoUpload()->url(upload_url(ArticleCategory::class, $form->getKey()));
                $form->switch('status');
                $form->display('created_at');
                $form->display('updated_at');

                $form->showFooter();
            });
            $form->saving(function (Form $form) {
                /** @var ArticleCategory $model */
                $model = $form->model();

                if (Request::has('parent_id')) {
                    $parentId = $form->input('parent_id');
                    $parent   = $model->find($parentId);
                    if ($parent) {
                        $model->appendToNode($parent)->save();
                    } else {
                        $model->saveAsRoot();
                    }
                } elseif (Request::has('_orderable')) {
                    $moveUp = $form->input('_orderable') == 1;
                    $node   = $model->find($form->getKey());
                    if ($moveUp) {
                        $node->up();
                    } else {
                        $node->down();
                    }
                } else {
                    $model->save();
                }

                return $model;
            });
        });
    }
}
