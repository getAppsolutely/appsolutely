<?php

namespace App\Admin\Controllers;

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
        $data       = request()->all();
        $controller = Str::before(class_basename($this), 'Controller');
        $model      = (new \ReflectionClass(Model::class))->getNamespaceName() . '\\' . $controller;
        if (! class_exists($model)) {
            $message = $model . __t(' Model not found');
            log_error($message);
            throw new \Exception($message);
        }
        $object     = (new $model())->find($id);
        $filterData = \Arr::except($data, self::UPDATE_TO_IGNORE_FIELDS);
        $object->setDirty($filterData)->update($filterData);

        return (new Form())->response()->success(trans('admin.update_succeeded'))->refresh();
    }

    protected function detail($id)
    {
        $routeName     = request()->route()->getName();
        $redirectRoute = str_replace('show', 'edit', $routeName);

        return redirect(route($redirectRoute, [$id]));
    }
}
