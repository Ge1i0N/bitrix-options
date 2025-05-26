<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Fields;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Gelion\BitrixOptions\Traits\WithModuleId;

class Checkbox extends Text
{
    use WithModuleId;

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
                            <div class="ui-ctl ui-ctl-checkbox {$this->getClassFromProps()}">
                                {$this->getTagView()}
                                <input  type="checkbox"
                                        class="ui-ctl-element"
                                        name="$this->key"
                                        value="$this->value"
                                        placeholder="$this->placeholder"
                                        autocomplete="$this->autocomplete"
                                        {$this->getCheckedView()}
                                        {$this->getDisabledView()}
                                        {$this->getReadonlyView()} />
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        HTML;
    }

    protected function getCheckedView(): string
    {
        return $this->value === 'Y' ? 'checked' : '';
    }

    public function saveFromRequest(): static
    {
        if (!check_bitrix_sessid()) return $this;

        $request = Application::getInstance()->getContext()->getRequest();
        if ($request->get('update') !== 'Y') return $this;

        $moduleId = $request->get('mid');
        if (!$moduleId) return $this;

        $value = is_null($request->get($this->key)) ? 'N' : 'Y';

        Option::set($moduleId, $this->key, $value);

        return $this;
    }
}



