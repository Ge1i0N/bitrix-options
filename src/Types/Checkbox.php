<?php

namespace Gelion\BitrixOptions\Types;

use Gelion\BitrixOptions\TypeForm;

class Checkbox extends TypeForm
{
    public function getHtml(): string
    {
        $class = $this->getClass();

        $value = htmlspecialchars($this->fields['VALUE']);
        $name = htmlspecialchars($this->fields['NAME']);
        $checked = $value === 'Y' ? 'checked' : '';

        return <<<HTML
            <div id="{$name}-block" class="{$name}-raw">
                <div class="{$name}-container">
                    <div class="ui-ctl ui-ctl-checkbox {$class}">
                        <input  type="checkbox"
                                class="ui-ctl-element"
                                name="{$name}"
                                value="{$value}"
                                {$checked}
                                />
                    </div>
                </div>
            </div>
        HTML;
    }

    public function parseValueString()
    {
        return strval($this->fields['VALUE']);
    }

    public function getValueString(): string
    {
        return is_null($this->fields['VALUE']) ? 'N' : 'Y';
    }
}
