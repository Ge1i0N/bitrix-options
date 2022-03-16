<?php

namespace Gelion\BitrixOptions\Types;

class Number extends Text
{
    public function getHtml(): string
    {
        $class = $this->getClass();
        $tag = $this->getTag();

        $value = htmlspecialchars($this->fields['VALUE']);
        $name = htmlspecialchars($this->fields['NAME']);
        $placeholder = htmlspecialchars($this->fields['PLACEHOLDER']);
        $disabled = $this->fields['DISABLED'] ? 'disabled' : '';
        $readonly = $this->fields['READONLY'] ? 'readonly' : '';
        $autocomplete = $this->fields['autocomplete'] ? 'on' : 'off';
        $step = $this->fields['STEP'] ? htmlspecialchars($this->fields['STEP']) : 1;
        $min = $this->fields['MIN'] ? htmlspecialchars($this->fields['MIN']) : '';
        $max = $this->fields['MAX'] ? htmlspecialchars($this->fields['MAX']) : '';

        return <<<HTML
            <div id="{$name}-block" class="{$name}-raw">
                <div class="{$name}-container">
                    <div class="ui-ctl ui-ctl-textbox {$class}">
                        {$tag}
                        <input  type="number"
                                class="ui-ctl-element"
                                name="{$name}"
                                value="{$value}"
                                step="{$step}"
                                min="{$min}"
                                max="{$max}"
                                placeholder="{$placeholder}"
                                autocomplete="{$autocomplete}"
                                {$disabled}
                                {$readonly}
                                />
                    </div>
                </div>
            </div>
        HTML;
    }
}
