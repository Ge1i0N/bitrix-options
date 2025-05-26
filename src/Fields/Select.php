<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Fields;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;

class Select extends Text
{
    public array $options = [];

    public bool $isMultiple = false;

    public ?int $size = null;

    public function multiple($isMultiple = true): static
    {
        $this->isMultiple = (bool) $isMultiple;

        return $this;
    }

    public function options($options): static
    {
        $this->options = $options;

        return $this;
    }

    public function size($size): static
    {
        $this->size = (int) $size;

        return $this;
    }

    public function view(): string
    {
        $angle = $this->isMultiple ? '' : ' <div class="ui-ctl-after ui-ctl-icon-angle"></div>';

        return <<<HTML
            <tr>
                <td>
                    $this->title
                    {$this->getHintView()}
                </td>
                <td nowrap>
                    <div id="$this->key-block">
                        <div class="$this->key-container">
                            <div class="ui-ctl {$this->getClassFromProps()}">
                                $angle
                                {$this->getTagView()}
                                <select class="ui-ctl-element"
                                        {$this->getNameView()}
                                        autocomplete="$this->autocomplete"
                                        {$this->getSizeView()}
                                        {$this->getMultipleView()}
                                        {$this->getDisabledView()}
                                        {$this->getReadonlyView()}>
                                            {$this->getOptionsView()}
                                </select>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        HTML;
    }

    protected function getClassFromProps(): string
    {
        $result = parent::getClassFromProps();

        if ($this->isMultiple) {
            $result .= 'ui-ctl-multiple-select ';
        } else {
            $result .= 'ui-ctl-after-icon ui-ctl-dropdown ';
        }

        return $result;
    }

    private function getNameView(): string
    {
        return sprintf(
            'name="%s"',
            $this->isMultiple ? $this->key . '[]' : $this->key,
        );
    }

    protected function getSizeView(): string
    {
        return $this->size ? sprintf('size="%s"', $this->size) : '';
    }

    protected function getMultipleView($isMultiple = true): string
    {
        return $this->isMultiple ? 'multiple' : '';
    }

    protected function getOptionsView(): string
    {
        $result = '';
        $value = $this->value ?? $this->default;

        foreach ($this->options as $key => $title) {
            $result .= sprintf(
                '<option value="%s" %s>%s</option>',
                $key,
                (is_array($value) ? in_array($key, $value) : $key == $value) ? 'selected' : '',
                $title
            );
        }

        return $result;
    }

    public function saveFromRequest(): static
    {
        if (!check_bitrix_sessid()) return $this;

        $request = Application::getInstance()->getContext()->getRequest();
        if ($request->get('update') !== 'Y') return $this;

        $moduleId = $request->get('mid');
        if (!$moduleId) return $this;

        $value = $request->get($this->key);
        if (!$value) return $this;

        if (is_array($value)) {
            Option::set($moduleId, $this->key, serialize($value));
        } else {
            Option::set($moduleId, $this->key, $value);
        }

        return $this;
    }

    public function fillValue(): static
    {
        $value = Option::get($this->moduleId, $this->key, $this->default);
        $unserialized = unserialize($value);

        $this->value = $unserialized !== false ? $unserialized : $value;

        return $this;
    }
}



