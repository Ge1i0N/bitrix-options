<?php

namespace Gelion\BitrixOptions\Types;

use Gelion\BitrixOptions\TypeForm;

class Colorpicker extends TypeForm
{
    public function getHtml(): string
    {
        $class = $this->getClass();
        $tag = $this->getTag();

        $value = htmlspecialchars($this->fields['VALUE']);
        $name = htmlspecialchars($this->fields['NAME']);
        $disabled = $this->fields['DISABLED'] ? 'disabled' : '';
        $readonly = $this->fields['READONLY'] ? 'readonly' : '';

        return <<<HTML
            <div id="{$name}-block" class="{$name}-raw">
                <div class="{$name}-container">
                    <div class="ui-ctl ui-ctl-textbox {$class}">
                        {$tag}
                        <input  type="color"
                                class="ui-ctl-element"
                                name="{$name}"
                                value="{$value}"
                                {$disabled}
                                {$readonly}
                                />
                    </div>
                </div>
            </div>
        HTML;
    }
}
