<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Fields;

use Gelion\BitrixOptions\Enums\AlertIcon;
use Gelion\BitrixOptions\Enums\AlertSize;
use Gelion\BitrixOptions\Enums\AlertTextPosition;
use Gelion\BitrixOptions\Enums\Color;
use Gelion\BitrixOptions\Traits\WithColor;
use Gelion\BitrixOptions\Traits\WithDisplayWithoutValue;
use Gelion\BitrixOptions\Traits\WithModuleId;
use Gelion\BitrixOptions\Traits\WithStyleClass;

/** @phpstan-consistent-constructor */
class Alert
{
    use WithModuleId, WithColor, WithStyleClass, WithDisplayWithoutValue;

    public ?AlertSize $size = null;

    public ?AlertIcon $icon = null;

    public ?AlertTextPosition $textPosition = null;

    public function __construct(public string $title)
    {
    }

    public static function make(string $title): static
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
        $title = $this->isHtmlAllowed()
            ? (string)$this->title
            : htmlspecialchars((string)$this->title, ENT_QUOTES, 'UTF-8');

        return <<<HTML
            <tr>
                <td colspan="2">
                    <div class="ui-alert {$this->getClassFromProps()} {$this->getAlertColorClass()}">
                        <span class="ui-alert-message">{$title}</span>
                    </div>
                </td>
            </tr>
        HTML;
    }

    /** @return string[] */
    protected function getStyleClassProperties(): array
    {
        return [
            'size',
            'icon',
            'textPosition',
        ];
    }

    private function getAlertColorClass(): string
    {
        if ($this->color === null) {
            return '';
        }

        return match ($this->color) {
            Color::ACTIVE => 'ui-alert-primary',
            Color::SUCCESS => 'ui-alert-success',
            Color::WARNING => 'ui-alert-warning',
            Color::DANGER => 'ui-alert-danger',
            Color::DISABLED, Color::HOVER => 'ui-alert-default',
        };
    }
}
