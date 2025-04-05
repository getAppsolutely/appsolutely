<?php

namespace App\Admin\Controllers;

use App\Helpers\DashboardHelper;
use App\Helpers\FileHelper;
use App\Helpers\TimeHelper;
use App\Models\File;
use App\Services\StorageService;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;
use Dcat\Admin\Traits\HasUploadedFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends AdminController
{
    use HasUploadedFile;

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new File(), function (Grid $grid) {
            $grid->column('id')->sortable();

            // Display file preview for images
            $grid->column('preview')->display(function () {
                return DashboardHelper::preview($this->path . '/' . $this->filename, $this->extension);
            });

            $grid->column('original_filename')->sortable();
            $grid->column('filename')->sortable();
            $grid->column('extension')->sortable();
            $grid->column('mime_type');

            // Use the helper to format the file size
            $grid->column('size')->display(function ($size) {
                return FileHelper::formatSize($size);
            })->sortable();

            // Format the timestamp using TimeHelper
            $grid->column('created_at')
                ->display(function ($timestamp) {
                    return TimeHelper::formatWithTz($timestamp);
                })->sortable();

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

        });
    }

    /**
     * Make a show builder.
     *
     * @param  mixed  $id
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new File(), function (Show $show) {
            $show->field('id');
            $show->field('original_filename');
            $show->field('filename');

            // Display file preview for images
            $show->field('preview')->unescape()->as(function () {
                return DashboardHelper::preview($this->path . '/' . $this->filename, $this->extension);
            });

            $show->field('extension');
            $show->field('mime_type');
            $show->field('path');

            // Use the helper to format the file size
            $show->field('size')->as(function ($size) {
                return FileHelper::formatSize($size);
            });

            $show->field('hash');

            // Format timestamps using TimeHelper
            $show->field('created_at')->as(function ($timestamp) {
                return TimeHelper::format($timestamp);
            });

            $show->field('updated_at')->as(function ($timestamp) {
                return TimeHelper::format($timestamp);
            });

            // Display related assets
            $show->relation('assets', function ($model) {
                $grid = new Grid(new \App\Models\Asset());
                $grid->model()->where('file_id', $model->id);
                $grid->column('id');
                $grid->column('assetable_type');
                $grid->column('assetable_id');
                $grid->column('type');
                $grid->column('title');
                $grid->column('created_at')->display(function ($timestamp) {
                    return TimeHelper::format($timestamp);
                });

                return $grid;
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new File(), function (Form $form) {
            if ($form->isCreating()) {
                // Use Dcat Admin's file upload component with multiple option
                $form->multipleFile('files', 'Upload Files')
                    ->required()
                    ->accept('*') // Accept all file types
                    ->autoUpload() // Enable auto upload
                    ->url(admin_url('files')) // Use our custom upload endpoint
                    ->uniqueName() // Generate unique names for files
                    ->help('Upload any file types. You can select multiple files at once.');

                $form->disableSubmitButton();
                $form->disableResetButton();

                $form->html('<div class="form-footer text-center">
                <a href="' . admin_url('files') . '" class="btn btn-primary">
                    <i class="feather icon-list"></i><span>&nbsp;Back to List</span>
                </a></div>');
            }

        });
    }

    /**
     * Handle file upload.
     *
     * @return JsonResponse
     */
    public function upload(Request $request)
    {
        $method = request()->method();
        if (request()->method() == 'GET') {
            return admin_redirect('files/manager');
        }
        if ($this->isDeleteRequest()) {
            // 删除文件并响应
            return $this->deleteFileAndResponse();
        }
        try {
            // Get the uploaded file
            $uploadedFile = $this->file();
            if (! $uploadedFile) {
                return admin_redirect('files/manager');
            }

            $uploader = $this->uploader();
            // Use the StorageService to store the file
            $storageService = app(StorageService::class);
            $file           = $storageService->store($uploadedFile);

            $path = $storageService->assessable($file, $uploader);
            return response()->json([
                'status'  => true,
                'data'    => [
                    'id'   => $path,
                    'name' => $file->filename,
                    'path' => $file->path,
                    'url'  => Storage::disk('s3')->url($file->full_path),
                ],
            ]);
        } catch (\Exception $e) {
            log_error('Upload failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
            ]);
        }
    }
}
