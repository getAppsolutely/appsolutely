<?php

declare(strict_types=1);

namespace App\Helpers;

final class AdminButtonHelper
{
    /**
     * Generate a button with JavaScript API call
     */
    public static function apiButton(array $options): string
    {
        $defaults = [
            'text'            => 'Action',
            'size'            => 'xs',
            'style'           => 'outline-primary',
            'icon'            => null,
            'confirm'         => null,
            'method'          => 'POST',
            'success_message' => null,
            'error_message'   => null,
            'refresh'         => true,
            'redirect'        => null,
            'class'           => '',
            'attributes'      => [],
        ];

        $config = array_merge($defaults, $options);

        // Validate required options
        if (empty($config['api_url'])) {
            throw new \InvalidArgumentException('api_url is required');
        }

        if (empty($config['function_name'])) {
            throw new \InvalidArgumentException('function_name is required');
        }

        // Generate unique function name if needed
        $functionName = $config['function_name'];

        // Build CSS classes
        $classes = ['btn', "btn-{$config['size']}", "btn-{$config['style']}"];
        if (! empty($config['class'])) {
            $classes[] = $config['class'];
        }

        $data = [
            'href'    => 'javascript:void(0)',
            'class'   => implode(' ', $classes),
            'onclick' => $functionName . '(' . ($config['payload'] ?? '') . ')',
        ];

        // Build button attributes
        $attributes = array_merge($config['attributes'], $data);

        // Build attribute string
        $attrString = '';
        foreach ($attributes as $key => $value) {
            $attrString .= " {$key}=\"" . htmlspecialchars($value) . '"';
        }

        // Build button content
        $content = '';
        if ($config['icon']) {
            $content .= "<i class=\"{$config['icon']}\"></i> ";
        }
        $content .= $config['text'];

        // Generate JavaScript function
        $jsFunction = self::generateJavaScriptFunction($functionName, $config);

        return $jsFunction . "\n<a{$attrString}>{$content}</a>";
    }

    /**
     * Generate delete button with confirmation
     */
    public static function deleteButton(int $id, string $apiUrl): string
    {
        return self::apiButton([
            'text'            => __t('Delete'),
            'icon'            => 'fa fa-trash',
            'style'           => 'outline-danger',
            'function_name'   => 'deleteItem',
            'api_url'         => $apiUrl,
            'payload'         => $id,
            'method'          => 'DELETE',
            'confirm'         => __t('Are you sure you want to delete this item? This action cannot be undone.'),
            'success_message' => __t('Item deleted successfully'),
            'error_message'   => __t('Failed to delete item'),
        ]);
    }

    /**
     * Generate toggle status button
     */
    public static function toggleStatusButton(int $id, string $apiUrl, bool $currentStatus): string
    {
        $action = $currentStatus ? 'disable' : 'enable';
        $style  = $currentStatus ? 'outline-warning' : 'outline-success';
        $icon   = $currentStatus ? 'fa fa-pause' : 'fa fa-play';

        return self::apiButton([
            'text'            => $currentStatus ? __t('Disable') : __t('Enable'),
            'icon'            => $icon,
            'style'           => $style,
            'function_name'   => 'toggleStatus',
            'api_url'         => $apiUrl,
            'payload'         => $id,
            'method'          => 'PATCH',
            'success_message' => $currentStatus ? __t('Item disabled') : __t('Item enabled'),
            'error_message'   => __t('Failed to update status'),
        ]);
    }

    /**
     * Generate duplicate button
     */
    public static function duplicateButton(int $id, string $apiUrl): string
    {
        return self::apiButton([
            'text'            => __t('Duplicate'),
            'icon'            => 'fa fa-copy',
            'style'           => 'outline-info',
            'function_name'   => 'duplicateItem',
            'api_url'         => $apiUrl,
            'payload'         => $id,
            'method'          => 'POST',
            'success_message' => __t('Item duplicated successfully'),
            'error_message'   => __t('Failed to duplicate item'),
        ]);
    }

    /**
     * Generate preview button
     */
    public static function previewButton(int $id, string $modalUrl): string
    {
        return self::apiButton([
            'text'            => __t('Preview'),
            'icon'            => 'fa fa-eye',
            'style'           => 'outline-primary',
            'function_name'   => 'previewItem',
            'api_url'         => $modalUrl,
            'payload'         => $id,
            'method'          => 'GET',
            'refresh'         => false,
            'success_message' => null,
            'error_message'   => __t('Failed to load preview'),
        ]);
    }

    /**
     * Generate JavaScript function for API calls
     */
    protected static function generateJavaScriptFunction(string $functionName, array $config): string
    {
        $hasPayload = isset($config['payload']);
        $paramList  = $hasPayload ? 'id' : '';

        $js = "<script>\n";
        $js .= "function {$functionName}({$paramList}) {\n";

        // Add confirmation if specified
        if ($config['confirm']) {
            $js .= "    if (!confirm('" . addslashes($config['confirm']) . "')) {\n";
            $js .= "        return false;\n";
            $js .= "    }\n\n";
        }

        // Build payload
        $js .= "    var payload = {};\n";
        if ($hasPayload) {
            $js .= "    payload.id = id;\n";
        }

        // Add any additional payload data
        if (isset($config['additional_payload']) && is_array($config['additional_payload'])) {
            foreach ($config['additional_payload'] as $key => $value) {
                $js .= "    payload.{$key} = " . json_encode($value) . ";\n";
            }
        }

        // AJAX call
        $js .= "\n    $.ajax({\n";
        $js .= "        url: '" . $config['api_url'] . "',\n";
        $js .= "        type: '" . $config['method'] . "',\n";
        $js .= "        data: payload,\n";
        $js .= "        headers: {\n";
        $js .= "            'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')\n";
        $js .= "        },\n";
        $js .= "        success: function(response) {\n";

        if ($config['success_message']) {
            $js .= "            Dcat.success('" . addslashes($config['success_message']) . "');\n";
        }

        if ($config['refresh']) {
            $js .= "            Dcat.reload();\n";
        }

        if ($config['redirect']) {
            $js .= "            window.location.href = '" . $config['redirect'] . "';\n";
        }

        // Handle preview/modal response
        if ($config['method'] === 'GET' && ! $config['refresh']) {
            $js .= "            if (response.data && response.data.content) {\n";
            $js .= "                var modal = $('<div class=\"modal fade\" tabindex=\"-1\">').html(response.data.content);\n";
            $js .= "                $('body').append(modal);\n";
            $js .= "                modal.modal('show');\n";
            $js .= "                modal.on('hidden.bs.modal', function() {\n";
            $js .= "                    modal.remove();\n";
            $js .= "                });\n";
            $js .= "            }\n";
        }

        $js .= "        },\n";
        $js .= "        error: function(xhr, status, error) {\n";

        if ($config['error_message']) {
            $js .= "            var message = '" . addslashes($config['error_message']) . "';\n";
            $js .= "            if (xhr.responseJSON && xhr.responseJSON.message) {\n";
            $js .= "                message = xhr.responseJSON.message;\n";
            $js .= "            }\n";
            $js .= "            Dcat.error(message);\n";
        } else {
            $js .= "            Dcat.error('An error occurred');\n";
        }

        $js .= "        }\n";
        $js .= "    });\n";
        $js .= "}\n";
        $js .= '</script>';

        return $js;
    }

    /**
     * Generate multiple buttons in a button group
     */
    public static function buttonGroup(array $buttons): string
    {
        $html = '<div class="btn-group" role="group">';

        foreach ($buttons as $button) {
            $html .= $button;
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Generate a custom API button with full control
     */
    public static function customButton(string $text, string $functionName, string $apiUrl, array $options = []): string
    {
        $config = array_merge([
            'text'          => $text,
            'function_name' => $functionName,
            'api_url'       => $apiUrl,
        ], $options);

        return self::apiButton($config);
    }
}
