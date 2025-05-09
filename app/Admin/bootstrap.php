<?php

use App\Admin\Extensions\Grid\QuickEditColumn;
use App\Admin\Extensions\Grid\SwitchableColumn;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;

/**
 * Dcat-admin - admin builder based on Laravel.
 *
 * @author jqh <https://github.com/jqhph>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 *
 * extend custom field:
 * Dcat\Admin\Form::extend('php', PHPEditor::class);
 * Dcat\Admin\Grid\Column::extend('php', PHPEditor::class);
 * Dcat\Admin\Grid\Filter::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 */
Grid::resolving(function (Grid $grid) {

    $grid->toolsWithOutline(false);

    $grid->disableViewButton(false);

    // $grid->rowSelector()->click();
    // $grid->model()->orderBy('id', 'DESC');

    $grid->filter(function (Grid\Filter $filter) {
        $filter->panel()->expand(false);
    });

    $grid->export();

    /*
    $grid->actions(function (Grid\Displayers\Actions $actions) {
        //$actions->disableView();
        $actions->disableEdit();
        //$actions->disableDelete();
    });
    */

    Admin::style('
    tr td.grid__actions__ a {
        display: block;
        margin-top: 5px;
        margin-bottom: 5px;
        min-width: 80px !important;
    }

    tr td.grid__actions__ a:hover span {
        text-decoration: underline;
    }

    tr td span.label {
        display: inline-block;
        padding: 6px;
    }
');

    Admin::script('
    $(document).ready(function() {
        if ($("table.table.data-table tr td i.fa.fa-angle-right").length > 0) {
            $("table.table.data-table tr td i.fa.fa-angle-right").click();
        }
    });
');

    Grid\Column::extend('quickEdit', QuickEditColumn::class);
    Grid\Column::extend('switchable', SwitchableColumn::class);

});
