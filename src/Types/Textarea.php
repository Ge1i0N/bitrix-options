<?php

namespace Gelion\BitrixOptions\Types;

use Gelion\BitrixOptions\TypeForm;

class Textarea extends TypeForm
{
    private $modificator = [
        'no-border',
        'no-padding',
        'underline',
        'round',
        'no-resize',
        'resize-y',
        'resize-x',
    ];

    public function setDefault(): void
    {
        $this->params = [
            'DISPLAY' => 'block',
            'WIDTH' => 'wa',
            'HEIGHT' => 'md',
            'COLOR' => 'default',
            'TAGCOLOR' => 'default',
            'MODIFICATOR' => false,
        ];
    }

    public function getHtml(): string
    {
        $class = $this->getClass();
        $tag = $this->getTag();

        $value = htmlspecialchars($this->fields['VALUE']);
        $name = htmlspecialchars($this->fields['NAME']);
        $placeholder = htmlspecialchars($this->fields['PLACEHOLDER']);
        $disabled = $this->fields['DISABLED'] ? 'disabled' : '';
        $readonly = $this->fields['READONLY'] ? 'readonly' : '';
        $autocomplete = $this->fields['AUTOCOMPLETE'] ? 'on' : 'off';
        $cols = $this->fields['COLS'] ? $this->fields['COLS'] : '';
        $rows = $this->fields['ROWS'] ? $this->fields['ROWS'] : '';

        return <<<HTML
            <div id="{$name}-block" class="{$name}-raw">
                <div class="{$name}-container">
                    <div class="ui-ctl ui-ctl-textbox {$class}">
                        {$tag}
                        <textarea class="ui-ctl-element"
                                style="white-space: pre-wrap; "
                                name="{$name}"
                                placeholder="{$placeholder}"
                                autocomplete="{$autocomplete}"
                                cols="{$cols}"
                                rows="{$rows}"
                                {$disabled}
                                {$readonly}
                                >{$value}</textarea>
                    </div>
                </div>
            </div>
        HTML;
    }
}
