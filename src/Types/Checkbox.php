<?php

namespace Gelion\BitrixOptions\Types;

use Gelion\BitrixOptions\TypeBase;
use Gelion\BitrixOptions\TypeInterface;

class Checkbox extends TypeBase implements TypeInterface
{
    public function getHtml(): string
    {
        $class = $this->getClass();

        $value = htmlspecialchars($this->fields['VALUE']);
        $name = htmlspecialchars($this->fields['NAME']);
        $checked = $value ? 'checked' : '';

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
}
