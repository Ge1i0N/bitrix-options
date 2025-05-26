<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Fields;

use Gelion\BitrixOptions\Enum\Resize;

class Textarea extends Text
{
    public ?Resize $resize = null;

    public ?int $rows = null;

    public ?int $cols = null;

    public function cols($count): static
    {
        $this->cols = (int) $count;

        return $this;
    }

    public function rows($count): static
    {
        $this->rows = (int) $count;

        return $this;
    }

    public function setResize(Resize $resize): static
    {
        $this->resize = $resize;

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
                            <div class="ui-ctl ui-ctl-textarea {$this->getClassFromProps()}">
                                {$this->getTagView()}
                                <textarea class="ui-ctl-element"
                                        name="$this->key"
                                        placeholder="$this->placeholder"
                                        autocomplete="$this->autocomplete"
                                        {$this->getRowsView()}
                                        {$this->getColsView()}
                                        {$this->getDisabledView()}
                                        {$this->getReadonlyView()} />$this->value</textarea>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        HTML;
    }

    protected function getHintView(): string
    {
        return mb_strlen(trim($this->hint)) > 0
            ? sprintf('<span data-hint="%s"></span>', htmlspecialchars($this->hint))
            : '';
    }

    protected function getRowsView(): string
    {
        return $this->rows ? sprintf('rows="%s"', $this->rows) : '';
    }

    protected function getColsView(): string
    {
        return $this->cols ? sprintf('cols="%s"', $this->cols) : '';
    }

    protected function getStyleClassProperties(): array
    {
        return array_merge(parent::getStyleClassProperties(), [
            'resize',
        ]);
    }
}



