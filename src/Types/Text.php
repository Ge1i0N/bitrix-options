<?php

namespace Gelion\BitrixOptions\Types;

use Gelion\BitrixOptions\TypeBase;
use Gelion\BitrixOptions\TypeInterface;

class Text extends TypeBase implements TypeInterface
{
    public function setDefault(): void
    {
        $this->params = [
            'DISPLAY' => 'inline',
            'WIDTH' => 'wd',
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

        return <<<HTML
            <div id="{$name}-block" class="{$name}-raw">
                <div class="{$name}-container">
                    <div class="ui-ctl ui-ctl-textbox {$class}">
                        {$tag}
                        <input  type="text"
                                class="ui-ctl-element"
                                name="{$name}"
                                value="{$value}"
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
