<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Fields;

use Gelion\BitrixOptions\Enum\AlertIcon;
use Gelion\BitrixOptions\Enum\AlertSize;
use Gelion\BitrixOptions\Enum\AlertTextPosition;
use Gelion\BitrixOptions\Traits\WithColor;
use Gelion\BitrixOptions\Traits\WithDisplayWithoutValue;
use Gelion\BitrixOptions\Traits\WithModuleId;
use Gelion\BitrixOptions\Traits\WithStyleClass;

class Alert
{
    use WithModuleId, WithColor, WithStyleClass, WithDisplayWithoutValue;

    public ?AlertSize $size = null;

    public ?AlertIcon $icon = null;

    public ?AlertTextPosition $textPosition = null;

    public function __construct(public $title)
    {
    }

    public static function make($title): static
    {
        return new static($title);
    }

    public function setIcon(AlertIcon $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function setSize(AlertSize $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function setTextPosition(AlertTextPosition $textPosition): static
    {
        $this->textPosition = $textPosition;

        return $this;
    }

    public function view(): string
    {
        return <<<HTML
            <tr>
                <td colspan="2">
                    <div class="ui-alert {$this->getClassFromProps()}">
                        <span class="ui-alert-message">$this->title</span>
                    </div>
                </td>
            </tr>
        HTML;
    }

    protected function getStyleClassProperties(): array
    {
        return [
            'size',
            'color',
            'icon',
            'textPosition',
        ];
    }
}
