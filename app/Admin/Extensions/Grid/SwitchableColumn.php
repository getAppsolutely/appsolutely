<?php

declare(strict_types=1);

namespace App\Admin\Extensions\Grid;

use Dcat\Admin\Grid\Displayers\AbstractDisplayer;

class SwitchableColumn extends AbstractDisplayer
{
    protected function getElementClass()
    {
        $name = $this->column->getName();

        return str_replace('.', '-', $name);
    }

    protected function script()
    {
        $endpoint = admin_route('api.common.quick-edit');

        return <<<JS
        $('.switchable-{$this->getElementClass()}').on('change', function(e) {
            e.stopPropagation();
            var target = $(this);
            var id = target.data('id');
            var field = target.data('field');
            var value = target.is(':checkbox') ? (target.prop('checked') ? 1 : 0) : target.val();
            var model = target.data('model');

            $.ajax({
                url: '{$endpoint}',
                method: 'POST',
                data: {
                    model: model,
                    id: id,
                    field: field,
                    value: value,
                    _token: Dcat.token
                },
                success: function(response) {
                    if (response.status) {
                        Dcat.success(response.message);
                    } else {
                        Dcat.error(response.message);
                        // Revert switch state if there's an error
                        target.prop('checked', !value);
                    }
                },
                error: function(xhr) {
                    Dcat.error(xhr.responseJSON.message || 'Error occurred');
                    // Revert switch state on error
                    target.prop('checked', !value);
                }
            });
        });

        $('head').append(`
            <style>
                .switchable-wrapper {
                    position: relative;
                    width: 36px;
                    height: 20px;
                    display: inline-block;
                }
                .switchable-wrapper input[type="checkbox"] {
                    opacity: 0;
                    width: 0;
                    height: 0;
                }
                .switchable-wrapper .slider {
                    position: absolute;
                    cursor: pointer;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: #ccc;
                    transition: .4s;
                    border-radius: 20px;
                }
                .switchable-wrapper .slider:before {
                    position: absolute;
                    content: "";
                    height: 16px;
                    width: 16px;
                    left: 2px;
                    bottom: 2px;
                    background-color: white;
                    transition: .4s;
                    border-radius: 50%;
                }
                .switchable-wrapper input:checked + .slider {
                    background-color: rgb(88, 108, 177);
                }
                .switchable-wrapper input:checked + .slider:before {
                    transform: translateX(16px);
                }
            </style>
        `);
        JS;
    }

    public function display($model = '')
    {
        $model   = $model ?: get_class($this->row);
        $value   = $this->value;
        $checked = $value ? 'checked' : '';

        admin_script($this->script());

        return <<<HTML
        <label class="switchable-wrapper">
            <input type="checkbox" class="switchable-{$this->getElementClass()}"
                   data-id="{$this->row->id}"
                   data-field="{$this->column->getName()}"
                   data-value="{$value}"
                   data-model="{$model}"
                   {$checked}>
            <span class="slider"></span>
        </label>
        HTML;
    }
}
