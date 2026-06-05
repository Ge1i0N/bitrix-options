<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Fields;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;

/** @phpstan-consistent-constructor */
class Select extends Text
{
    /** @var array<string, string> */
    public array $options = [];

    public bool $isMultiple = false;

    public ?int $size = null;

    public function multiple(bool $isMultiple = true): static
    {
        $this->isMultiple = (bool) $isMultiple;

        return $this;
    }

    /**
     * @param array<string, string> $options
     */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function size(int $size): static
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
                    {$this->escape($this->title)}
                    {$this->getHintView()}
                </td>
                <td nowrap>
                    <div id="{$this->escape($this->key)}-block">
                        <div class="{$this->escape($this->key)}-container">
                            <div class="ui-ctl {$this->getClassFromProps()}">
                                $angle
                                {$this->getTagView()}
                                <select class="ui-ctl-element"
                                        {$this->getNameView()}
                                        autocomplete="{$this->escape($this->autocomplete)}"
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

    protected function getMultipleView(bool $isMultiple = true): string
    {
        return $this->isMultiple ? 'multiple' : '';
    }

    protected function getOptionsView(): string
    {
        $result = '';
        $value = $this->value ?? $this->default;
        $stringValue = is_scalar($value) ? (string) $value : '';

        foreach ($this->options as $key => $title) {
            $result .= sprintf(
                '<option value="%s" %s>%s</option>',
                $this->escape($key),
                (is_array($value) ? in_array($key, $value) : (string)$key === $stringValue) ? 'selected' : '',
                $this->escape($title)
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
        if (!is_string($moduleId) || $moduleId === '') return $this;

        $value = $request->get($this->key);
        if ($value === null || $value === '') return $this;

        if (is_array($value)) {
            Option::set($moduleId, $this->key, serialize($value));
        } elseif (is_scalar($value)) {
            Option::set($moduleId, $this->key, (string)$value);
        }

        return $this;
    }

    public function fillValue(): static
    {
        $value = Option::get($this->moduleId, $this->key, $this->default);
        $unserialized = @unserialize($value);

        $this->value = ($unserialized !== false || $value === serialize(false)) ? $unserialized : $value;

        return $this;
    }
}



