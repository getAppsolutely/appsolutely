<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\NestedSetForm as Form;
use App\Models\ProductCategory;
use App\Repositories\ProductCategoryRepository;
use Dcat\Admin\Form\BlockForm;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Tree;
use Illuminate\Support\Facades\Request;

class ProductCategoryController extends AdminBaseController
{
    public function __construct(protected ProductCategoryRepository $productCategoryRepository) {}

    public function index1(Content $content)
    {
        return $content
            ->header('Product Categories')
            ->body(function (Row $row) {
                $tree = new Tree(new ProductCategory());
                $tree->disableSaveButton();
                $row->column(12, $tree);
            });
    }

    /**
     * Make a grid builder.
     */
    protected function grid(): Grid
    {
        return Grid::make(new ProductCategory(), function (Grid $grid) {

            $grid->column('id', __t('ID'))->width('50px');
            $grid->column('title', __t('Title'))->tree(true)->width('400px');
            $grid->column('children', __t('Children'))->display(function () {
                return $this->children()->count();
            })->width('80px')->setAttributes(children_attributes());
            $grid->column('status', __t('Status'))->switch();
            $grid->column('slug', __t('Slug'))->textarea()->width('240px');
            $grid->order->orderable();
            $grid->model()->orderBy('left', 'ASC');

            $grid->enableDialogCreate();
            $grid->setDialogFormDimensions('85%', '85%');

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
     * Make a form builder.
     */
    protected function form(): Form
    {
        return Form::make(new ProductCategory(), function (Form $form) {
            $form->block(7, function (BlockForm $form) {
                $form->title(__('Basic'));

                $form->display('id');

                $availableCategories = $this->productCategoryRepository->getActiveList();
                $form->select('parent_id', 'Parent')->options($availableCategories);

                $form->text('title')->required();
                $form->text('slug');

                $form->textarea('keywords')->rows(3);
                $form->textarea('description')->rows(5);
            });

            $form->block(5, function (BlockForm $form) {
                $form->title('Optional');
                $form->keyValue('setting')->default([])->setKeyLabel('Key')->setValueLabel('Value')->saveAsJson();
                $form->image('cover')->autoUpload()->url(upload_to_api(ProductCategory::class, $form->getKey()));
                $form->switch('status');
                $form->showFooter();
            });
            $form->saving(function (Form $form) {
                /** @var ProductCategory $model */
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
