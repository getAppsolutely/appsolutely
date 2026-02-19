<?php

declare(strict_types=1);

namespace App\Admin\Extensions\Grid;

use Dcat\Admin\Grid\Displayers\SwitchDisplay as BaseSwitchDisplay;

/**
 * Extends Dcat's SwitchDisplay so that BackedEnum (e.g. Status) is normalized
 * to its scalar value. Otherwise enum instances are truthy in PHP and the
 * switch always appears "on" in the list.
 */
final class SwitchDisplay extends BaseSwitchDisplay
{
    public function display(string $color = '', $refresh = false)
    {
        $value = $this->value;
        if ($value instanceof \BackedEnum) {
            $value = $value->value;
        }
        $this->value = $value;

        return parent::display($color, $refresh);
    }
}
