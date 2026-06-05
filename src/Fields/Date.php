<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Fields;

class Date extends Text
{
    public function view(): string
    {
        return <<<HTML
            <tr>
                <td>
                    {$this->escape($this->title)}
                    {$this->getHintView()}
                </td>
                <td nowrap>
                    <div id="{$this->escape($this->key)}-block">
                        <div class="{$this->escape($this->key)}-container">
                            <div class="ui-ctl ui-ctl-textbox {$this->getClassFromProps()}">
                                {$this->getTagView()}
                                <input  type="date"
                                        class="ui-ctl-element"
                                        name="{$this->escape($this->key)}"
                                        value="{$this->escape($this->value)}"
                                        placeholder="{$this->escape($this->placeholder)}"
                                        autocomplete="{$this->escape($this->autocomplete)}"
                                        {$this->getDisabledView()}
                                        {$this->getReadonlyView()} />
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        HTML;
    }
}



