<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Fields;

use Gelion\BitrixOptions\Traits\WithDisplayWithoutValue;
use Gelion\BitrixOptions\Traits\WithModuleId;

/** @phpstan-consistent-constructor */
class Heading
{
    use WithModuleId, WithDisplayWithoutValue;

    public function __construct(public string $title)
    {
    }

    public static function make(string $title): static
    {
        return new static($title);
    }

    public function view(): string
    {
        $title = $this->isHtmlAllowed()
            ? (string)$this->title
            : htmlspecialchars((string)$this->title, ENT_QUOTES, 'UTF-8');

        return <<<HTML
            <tr class="heading">
                <td colspan="2">
                    <b>{$title}</b>
                </td>
            </tr>
        HTML;
    }
}
