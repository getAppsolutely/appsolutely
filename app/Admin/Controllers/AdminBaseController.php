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
     * @throws \Exception
     */
    public function update($id)
    {
        if (! request()->get('_inline_edit_')) {
            return parent::update($id);
        }

        $data       = request()->all();
        $controller = Str::before(class_basename($this), 'Controller');
        $model      = (new \ReflectionClass(Model::class))->getNamespaceName() . '\\' . $controller;
        if (! class_exists($model)) {
            $message = "Model class '{$model}' not found for controller '{$controller}'";
            log_error($message);
            throw new NotFoundException($message);
        }
        $object     = (new $model())->find($id);
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
