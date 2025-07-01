<?php

namespace App\Admin\Forms\Models;

use App\Models\Model;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

class ModelForm extends Form implements LazyRenderable
{
    use LazyWidget;

    protected ?int $id;

    protected ?Model $model;

    public function __construct(?int $id = null)
    {
        parent::__construct();
        $this->id = $id;
    }

    public function handle(array $input)
    {
        $id        = $this->payload['id'] ?? null;
        if ($id) {
            $this->model->find($id)->update($input);

            return $this->response()->success(__t('Updated successfully'))->refresh();
        }

        $this->model->create($input);

        return $this->response()->success(__t('Created successfully'))->refresh();
    }

    public function form(): void
    {
        $id        = $this->payload['id'] ?? null;

        if ($id) {
            $data = $this->model->find($id);
            $this->fill($data);
        }
    }
}
