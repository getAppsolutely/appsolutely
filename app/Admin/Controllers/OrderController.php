<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;

class OrderController extends AdminBaseController
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    protected function grid(): Grid
    {
        return Grid::make(Order::with(['user']), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('reference');
            $grid->column('user_id');
            $grid->column('summary');
            $grid->column('amount');
            $grid->column('discounted_amount');
            $grid->column('total_amount');
            $grid->column('status')->label();
            $grid->column('created_at')->display(column_time_format())->sortable();

            $grid->quickSearch('id', 'reference', 'user_id');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(3);
                $filter->like('reference')->width(3);
                $filter->equal('user_id')->width(3);
                $filter->equal('status')->width(3);
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
            });

            $grid->model()->orderBy('id', 'desc');
        });
    }

    protected function detail(mixed $id): Show
    {
        return Show::make($id, new Order(), function (Show $show) {
            $show->field('id');
            $show->field('reference');
            $show->field('user_id');
            $show->field('summary');
            $show->field('amount');
            $show->field('discounted_amount');
            $show->field('total_amount');
            $show->field('status');
            $show->field('delivery_info');
            $show->field('note');
            $show->field('remark');
            $show->field('ip');
            $show->field('request');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    protected function form(): Form
    {
        return Form::make(Order::with(['user']), function (Form $form) {
            $form->display('id');
            $form->text('reference')->required();
            $form->text('user_id')->required();
            $form->text('summary');
            $form->number('amount');
            $form->number('discounted_amount');
            $form->number('total_amount');
            $form->text('status');
            $form->textarea('delivery_info');
            $form->textarea('note');
            $form->textarea('remark');
            $form->text('ip');
            $form->textarea('request');
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
