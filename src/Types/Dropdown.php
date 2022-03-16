<?php

namespace Gelion\BitrixOptions\Types;

use Gelion\BitrixOptions\TypeForm;

class Dropdown extends TypeForm
{
    public function getHtml(): string
    {
        $class = $this->getClass();
        $tag = $this->getTag();

        $value = htmlspecialchars($this->fields['VALUE']);
        $name = htmlspecialchars($this->fields['NAME']);
        $options = $this->getOptionsHtml($this->fields['OPTIONS'], $value);

        return <<<HTML
            <div id="{$name}-block" class="{$name}-raw">
                <div class="{$name}-container">
                    <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown {$class}">
                        {$tag}
                        <div class="ui-ctl-after ui-ctl-icon-angle"></div>
                        <select class="ui-ctl-element" name="{$name}">
                            {$options}
                        </select>
                    </div>
                </div>
            </div>
        HTML;
    }

    protected function getOptionsHtml($options = [], $value = null): string
    {
        $string = '';
        if (!empty($options))
            foreach ($options as $option) {
                $selected = (!is_null($value) && $option['VALUE'] === $value) ? 'selected' : '';
                $string .= '<option value="' . $option['VALUE'] . '"'  . $selected . '>' . $option['TITLE'] . '</option>';
            }

        return $string;
    }
}
