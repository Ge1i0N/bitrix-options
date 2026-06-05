<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Fields;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Gelion\BitrixOptions\Enums\Display;
use Gelion\BitrixOptions\Enums\Height;
use Gelion\BitrixOptions\Enums\TextStyle;
use Gelion\BitrixOptions\Enums\Width;
use Gelion\BitrixOptions\Traits\WithColor;
use Gelion\BitrixOptions\Traits\WithHint;
use Gelion\BitrixOptions\Traits\WithModuleId;
use Gelion\BitrixOptions\Traits\WithStyleClass;
use Gelion\BitrixOptions\Traits\WithTag;

/** @phpstan-consistent-constructor */
class Text
{
    use WithModuleId, WithTag, WithHint, WithColor, WithStyleClass;

    public mixed $value = '';

    public mixed $default = '';

    public string $placeholder = '';

    public string $autocomplete = 'off';

    public bool $disabled = false;

    public bool $readonly = false;

    public ?Display $display = null;

    public ?Width $width = null;

    public ?Height $height = null;

    public ?TextStyle $style = null;

    public function __construct(
        public string $key,
        public string $title,
    ) {
    }

    public function disabled(bool $value = true): static
    {
        $this->disabled = $value;

        return $this;
    }

    public function fillValue(): static
    {
        $this->value = Option::get($this->moduleId, $this->key, $this->default);

        return $this;
    }

    public static function make(string $key, ?string $title = null): static
    {
        $class = new static($key, $title ?? $key);

        $class->saveFromRequest();

        return $class;
    }

    public function saveFromRequest(): static
    {
        if (!check_bitrix_sessid()) return $this;

        $request = Application::getInstance()->getContext()->getRequest();
        if ($request->get('update') !== 'Y') return $this;

        $moduleId = $request->get('mid');
        if (!is_string($moduleId) || $moduleId === '') return $this;

        $value = $request->get($this->key);
        if (!is_string($value)) {
            $value = '';
        }

        Option::set($moduleId, $this->key, $value);

        return $this;
    }

    public function readonly(bool $value = true): static
    {
        $this->readonly = $value;

        return $this;
    }

    public function setAutocomplete(string $autocomplete = 'off'): static
    {
        $this->autocomplete = $autocomplete;

        return $this;
    }

    public function setDefault(mixed $default): static
    {
        $this->default = $default;

        return $this;
    }

    public function setDisplay(Display $display): static
    {
        $this->display = $display;

        return $this;
    }

    public function setHeight(Height $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function setPlaceholder(string $placeholder = ''): static
    {
        $this->placeholder = (string) $placeholder;

        return $this;
    }

    public function setStyle(TextStyle $style): static
    {
        $this->style = $style;

        return $this;
    }

    public function setWidth(Width $width): static
    {
        $this->width = $width;

        return $this;
    }

    protected function escape(mixed $value): string
    {
        if (is_string($value)) {
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }

        if (is_int($value) || is_float($value)) {
            return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        }

        if (is_bool($value)) {
            return htmlspecialchars($value ? '1' : '', ENT_QUOTES, 'UTF-8');
        }

        return '';
    }

    public function view(): string
    {
        return <<<HTML
            <tr>
                <td>
                    {$this->escape((string)$this->title)}
                    {$this->getHintView()}
                </td>
                <td nowrap>
                    <div id="{$this->escape($this->key)}-block">
                        <div class="{$this->escape($this->key)}-container">
                            <div class="ui-ctl ui-ctl-textbox {$this->getClassFromProps()}">
                                {$this->getTagView()}
                                <input  type="text"
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

    protected function getDisabledView(): string
    {
        return $this->disabled ? 'disabled' : '';
    }

    protected function getReadonlyView(): string
    {
        return $this->readonly ? 'readonly' : '';
    }

    /**
     * @return string[]
     */
    protected function getStyleClassProperties(): array
    {
        return [
            'display',
            'width',
            'height',
            'color',
            'style',
        ];
    }
}



