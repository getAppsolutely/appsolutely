<?php

declare(strict_types=1);

namespace App\Admin\Extensions\Grid;

use Dcat\Admin\Grid\Displayers\AbstractDisplayer;

class QuickEditColumn extends AbstractDisplayer
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
        $('.quick-edit-{$this->getElementClass()}').on('click', function(e) {
            e.stopPropagation();
            var target = $(this);
            var id = target.data('id');
            var field = target.data('field');
            var value = target.data('value');
            var model = target.data('model');
            var width = Math.max(target.width(), 100); // Minimum width of 100px

            // Remove any existing popovers
            $('.quick-edit-popover').remove();

            // Create popover container
            var popover = $('<div class="quick-edit-popover"></div>');

            // Create input group
            var inputGroup = $('<div class="input-group"></div>');
            var input = $('<input type="text" class="form-control quick-edit-input">').val(value);
            var btnGroup = $('<div class="input-group-btn"></div>');
            var cancelBtn = $('<button type="button" class="btn btn-default btn-sm">Cancel</button>');
            var submitBtn = $('<button type="button" class="btn btn-primary btn-sm">Submit</button>');

            // Add elements to DOM
            btnGroup.append(cancelBtn).append(submitBtn);
            inputGroup.append(input).append(btnGroup);
            popover.append(inputGroup);

            // Position popover
            var pos = target.offset();
            popover.css({
                top: pos.top + target.outerHeight(),
                left: pos.left
            });

            // Add to body and focus input
            $('body').append(popover);
            input.focus();

            // Set cursor position to end
            var inputLength = input.val().length;
            input[0].setSelectionRange(inputLength, inputLength);

            // Handle submit
            function submitValue() {
                var newValue = input.val();
                if (newValue !== value) {
                    $.ajax({
                        url: '{$endpoint}',
                        method: 'POST',
                        data: {
                            model: model,
                            id: id,
                            field: field,
                            value: newValue,
                            _token: Dcat.token
                        },
                        success: function(response) {
                            if (response.status) {
                                Dcat.success(response.message);
                                // Refresh the grid to show updated data
                                Dcat.reload();
                            } else {
                                Dcat.error(response.message);
                            }
                        },
                        error: function(xhr) {
                            Dcat.error(xhr.responseJSON.message || 'Error occurred');
                        }
                    });
                }
                popover.remove();
            }

            // Event handlers
            submitBtn.on('click', submitValue);
            cancelBtn.on('click', function() {
                popover.remove();
            });

            input.on('keyup', function(e) {
                if (e.key === 'Enter') {
                    submitValue();
                }
                if (e.key === 'Escape') {
                    popover.remove();
                }
            });

            // Close popover when clicking outside
            $(document).on('click.quick-edit', function(e) {
                if (!$(e.target).closest('.quick-edit-popover').length) {
                    popover.remove();
                    $(document).off('click.quick-edit');
                }
            });
        });

        // Add necessary styles
        $('head').append(`
            <style>
                .quick-edit-popover {
                    min-width: 150px;
                    position: absolute;
                    z-index: 1000;
                    background: white;
                    padding: 8px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
                .quick-edit-popover .input-group {
                    width: 100%;
                }
                .quick-edit-popover .form-control {
                    width: 100%;
                    margin-bottom: 5px;
                }
                .quick-edit-popover .input-group-btn {
                    display: flex;
                    justify-content: space-between;
                    white-space: nowrap;
                    margin-top: 5px;
                    width: 100%;
                }
                .quick-edit-wrapper {
                    display: inline-block;
                    position: relative;
                    padding-right: 16px;
                }
                .quick-edit-wrapper:hover .quick-edit-icon {
                    opacity: 1;
                }
                .quick-edit-wrapper a {
                    border-bottom: 1px dashed #ccc;
                    transition: border-color 0.2s ease;
                }
                .quick-edit-wrapper:hover a {
                    border-bottom-color: #666;
                }
                .quick-edit-icon {
                    position: absolute;
                    right: 0;
                    top: 50%;
                    transform: translateY(-50%);
                    opacity: 0;
                    transition: opacity 0.2s ease;
                    color: #666;
                }
            </style>
        `);
        JS;
    }

    public function display($model = '')
    {
        $model = $model ?: get_class($this->row);
        $value = $this->value;

        // Add script to page
        admin_script($this->script());

        return "<div class=\"quick-edit-wrapper\">
                    <a href=\"javascript:void(0);\" class=\"quick-edit-{$this->getElementClass()}\" data-id=\"{$this->row->id}\" data-field=\"{$this->column->getName()}\" data-value=\"{$value}\" data-model=\"{$model}\">{$value}
                    <i class=\"fa fa-pencil quick-edit-icon\"></i></a>
                </div>";
    }
}
