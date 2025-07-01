<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

class OrderController extends AdminBaseController
{
    public function __construct(protected OrderService $orderService) {}

    protected function grid(): Grid
    {
        return Grid::make(Order::with(['user']), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('reference', __t('Reference'));
            $grid->column('user_id', __t('User ID'));
            $grid->column('summary', __t('Summary'));
            $grid->column('amount', __t('Amount'));
            $grid->column('discounted_amount', __t('Discounted Amount'));
            $grid->column('total_amount', __t('Total Amount'));
            $grid->column('status', __t('Status'))->label();
            $grid->model()->orderByDesc('id');

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

            $grid->disableCreateButton();
        });
    }

    protected function form(): Form
    {
        return Form::make(Order::with(['user']), function (Form $form) {
            $form->display('id', __t('ID'));
            $form->text('reference', __t('Reference'))->required();
            $form->text('user_id', __t('User ID'))->required();
            $form->text('summary', __t('Summary'));
            $form->number('amount', __t('Amount'));
            $form->number('discounted_amount', __t('Discounted Amount'));
            $form->number('total_amount', __t('Total Amount'));
            $form->text('status', __t('Status'));
            $form->textarea('delivery_info', __t('Delivery Info'));
            $form->textarea('note', __t('Note'));
            $form->textarea('remark', __t('Remark'));
            $form->text('ip', __t('IP'));
            $form->textarea('request', __t('Request'));
        });
    }
}
