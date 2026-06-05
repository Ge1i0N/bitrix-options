<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Fields;

class Number extends Text
{
    public ?string $min = null;

    public ?string $max = null;

    public ?string $step = null;

    public function max(int|float $max): static
    {
        $this->max = (string) $max;

        return $this;
    }

    public function min(int|float $min): static
    {
        $this->min = (string) $min;

        return $this;
    }

    public function step(int|float $step): static
    {
        $this->step = (string) $step;

        return $this;
    }

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
                                <input  type="number"
                                        class="ui-ctl-element"
                                        name="{$this->escape($this->key)}"
                                        value="{$this->escape($this->value)}"
                                        placeholder="{$this->escape($this->placeholder)}"
                                        autocomplete="{$this->escape($this->autocomplete)}"
                                        {$this->getMinView()}
                                        {$this->getMaxView()}
                                        {$this->getStepView()}
                                        {$this->getDisabledView()}
                                        {$this->getReadonlyView()} />
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        HTML;
    }

    protected function getMinView(): string
    {
        return $this->min !== null ? sprintf('min="%s"', $this->escape($this->min)) : '';
    }

    protected function getMaxView(): string
    {
        return $this->max !== null ? sprintf('max="%s"', $this->escape($this->max)) : '';
    }

    protected function getStepView(): string
    {
        return $this->step !== null ? sprintf('step="%s"', $this->escape($this->step)) : '';
    }
}



