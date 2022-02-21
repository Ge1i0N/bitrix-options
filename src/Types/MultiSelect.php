<?php

namespace Gelion\BitrixOptions\Types;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class MultiSelect extends Dropdown
{
    public function getHtml(): string
    {
        $class = $this->getClass();
        $tag = $this->getTag();

        $value = $this->fields['VALUE'];
        $name = htmlspecialchars($this->fields['NAME']);
        $options = $this->getOptionsHtml($this->fields['OPTIONS'], $value);
        $size = $this->fields['SIZE'] ? htmlspecialchars($this->fields['SIZE']) : 5;

        return <<<HTML
            <div id="{$name}-block" class="{$name}-raw">
                <div class="{$name}-container">
                    <div class="ui-ctl ui-ctl-multiple-select {$class}">
                        {$tag}
                        <select class="ui-ctl-element" name="{$name}[]" size="{$size}" multiple>
                            {$options}
                        </select>
                    </div>
                </div>
            </div>
        HTML;
    }

    protected function getOptionsHtml($options = [], $value = []): string
    {
        $string = '';
        if (empty($options))
            $string .= '<option value="" selected>' . Loc::getMessage('empty_array') . '</option>';
        else
            foreach ($options as $option) {
                $selected = (!empty($value) && in_array($option['VALUE'], $value)) ? 'selected' : '';
                $string .= '<option value="' . $option['VALUE'] . '"'  . $selected . '>' . $option['TITLE'] . '</option>';
            }

        return $string;
    }
}
