<?php

namespace App\Admin\Controllers;

use App\Helpers\DashboardHelper;
use App\Models\File;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;

class FileController extends AdminBaseController
{
    /**
     * Make a grid builder.
     */
    protected function grid(): Grid
    {
        return Grid::make(new File(), function (Grid $grid) {
            $grid->column('id')->sortable();

            // Display file preview for images
            $grid->column('preview')->display(function () {
                return DashboardHelper::imageThumbnail($this->full_path);
            });

            $grid->column('filename')->width('120px')->sortable();
            $grid->column('original_filename')->width('80px')->sortable();
            $grid->column('extension')->sortable();
            $grid->column('mime_type')->width('80px');

            // Use the helper to format the file size
            $grid->column('size')->display(column_file_size())->sortable();

            $grid->column('created_at')->display(column_time_format())->sortable();

            $grid->quickSearch('id', 'original_filename', 'filename', 'extension');

            // Add filter
            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('original_filename')->width(4);
                $filter->like('filename')->width(4);
                $filter->equal('extension')->width(4);
                $filter->like('mime_type')->width(4);
                $filter->between('created_at')->datetime()->width(4);
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableEdit();
            });

            $grid->model()->orderBy('id', 'DESC');
        });
    }

    /**
     * Make a show builder.
     */
    protected function detail(mixed $id): Show
    {
        return Show::make($id, new File(), function (Show $show) {
            $show->field('id');
            $show->field('original_filename');
            $show->field('filename');

            // Display file preview for images
            $show->field('preview')->unescape()->as(function () {
                return DashboardHelper::imageThumbnail($this->full_path);
            });

            $show->field('extension');
            $show->field('mime_type');
            $show->field('path');

            // Use the helper to format the file size
            $show->field('size')->as(column_file_size());

            $show->field('hash');
            $show->field('created_at')->as(column_time_format());
            $show->field('updated_at')->as(column_time_format());

            // Display related
            $show->relation('Assessable', function ($model) {
                $grid = new Grid(new \App\Models\Assessable());
                $grid->model()->where('file_id', $model->id);
                $grid->column('id');
                $grid->column('file_path');
                $grid->column('assessable_type');
                $grid->column('assessable_id');
                $grid->column('type');
                $grid->column('created_at')->display(column_time_format());
                $grid->disableActions();

                return $grid;
            });
        });
    }

    /**
     * Make a form builder.
     */
    protected function form(): Form
    {
        return Form::make(new File(), function (Form $form) {
            if ($form->isCreating()) {
                // Use Dcat Admin's file upload component with multiple option
                $form->multipleFile('files', 'Upload Files')
                    ->required()
                    ->accept('*') // Accept all file types
                    ->autoUpload() // Enable auto upload
                    ->url(upload_url()) // Use our custom upload endpoint
                    ->uniqueName() // Generate unique names for files
                    ->help('Upload any file types. You can select multiple files at once.');

                $form->disableSubmitButton();
                $form->disableResetButton();

                $form->html('<div class="form-footer text-center">
                <a href="' . admin_url('files/manager') . '" class="btn btn-primary">
                    <i class="feather icon-list"></i><span>&nbsp;Back to List</span>
                </a></div>');
            }

        });
    }
}
