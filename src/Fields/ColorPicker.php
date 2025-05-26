<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Fields;

class ColorPicker extends Text
{
    public function view(): string
    {
        return <<<HTML
            <tr>
                <td>
                    $this->title
                    {$this->getHintView()}
                </td>
                <td nowrap>
                    <div id="$this->key-block">
                        <div class="$this->key-container">
                            <div class="ui-ctl ui-ctl-textbox {$this->getClassFromProps()}">
                                {$this->getTagView()}
                                <input  type="color"
                                        class="ui-ctl-element"
                                        style="flex: none"
                                        name="$this->key"
                                        value="$this->value"
                                        placeholder="$this->placeholder"
                                        autocomplete="$this->autocomplete"
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



