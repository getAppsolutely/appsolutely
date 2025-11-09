<?php

namespace App\Admin\Controllers\Api;

use Illuminate\Http\Request;

final class CommonController extends AdminBaseApiController
{
    public function quickEdit(Request $request)
    {
        try {
            $model = $request->input('model');
            $id    = $request->input('id');
            $field = $request->input('field');
            $value = $request->input('value');

            if (! class_exists($model)) {
                return $this->failForbidden(__t('Invalid model class'));
            }

            $instance = app($model);
            $record   = $instance->findOrFail($id);

            // Update the field
            $record->$field = $value;
            $record->save();

            return $this->success(null, __t('Updated successfully'));
        } catch (\Exception $e) {
            log_error('quickEdit Exception: ' . $e->getMessage(), $e->getTrace());

            return $this->error($e->getMessage());
        }
    }
}
