<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Fields;

class Number extends Text
{
    public ?string $min = null;

    public ?string $max = null;

    public ?string $step = null;

    public function max($max): static
    {
        $this->max = (string) $max;

        return $this;
    }

    public function min($min): static
    {
        $this->min = (string) $min;

        return $this;
    }

    public function step($step): static
    {
        $this->step = (string) $step;

        return $this;
    }

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
                                <input  type="number"
                                        class="ui-ctl-element"
                                        name="$this->key"
                                        value="$this->value"
                                        placeholder="$this->placeholder"
                                        autocomplete="$this->autocomplete"
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
        return $this->min ? sprintf('min="%s"', $this->min) : '';
    }

    protected function getMaxView(): string
    {
        return $this->max ? sprintf('max="%s"', $this->max) : '';
    }

    protected function getStepView(): string
    {
        return $this->step ? sprintf('step="%s"', $this->step) : '';
    }
}



