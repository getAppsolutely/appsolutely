<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Exceptions\NotFoundException;
use App\Models\Model;
use Dcat\Admin\Form;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Str;

class AdminBaseController extends AdminController
{
    const UPDATE_TO_IGNORE_FIELDS = ['_inline_edit_', '_method', '_token'];

    protected function form() {}

    /**
     * Get the model class name for this controller.
     * Override this method in child controllers to explicitly define the model.
     *
     * @return string Fully qualified model class name
     */
    protected function getModelClass(): string
    {
        // Fallback to reflection-based resolution for backward compatibility
        // Child controllers should override this method for explicit definition
        $controller = Str::before(class_basename($this), 'Controller');
        $model      = (new \ReflectionClass(Model::class))->getNamespaceName() . '\\' . $controller;

        if (! class_exists($model)) {
            $message = "Model class '{$model}' not found for controller '{$controller}'. " .
                       'Please override getModelClass() method in ' . get_class($this);
            log_error($message);
            throw new NotFoundException($message);
        }

        return $model;
    }

    /**
     * @throws \Exception
     */
    public function update($id)
    {
        if (! request()->get('_inline_edit_')) {
            return parent::update($id);
        }

        $data       = request()->all();
        $modelClass = $this->getModelClass();
        $object     = (new $modelClass())->find($id);
        $filterData = \Arr::except($data, self::UPDATE_TO_IGNORE_FIELDS);
        $object->update($filterData);

        return (new Form())->response()->success(trans('admin.update_succeeded'))->refresh();
    }

    protected function detail($id)
    {
        $routeName     = request()->route()->getName();
        $redirectRoute = str_replace('show', 'edit', $routeName);

        return redirect(route($redirectRoute, [$id]));
    }
}
