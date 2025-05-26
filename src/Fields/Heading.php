<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Fields;

use Gelion\BitrixOptions\Traits\WithDisplayWithoutValue;
use Gelion\BitrixOptions\Traits\WithModuleId;

class Heading
{
    use WithModuleId, WithDisplayWithoutValue;

    public function __construct(public $title)
    {
    }

    public static function make($title): static
    {
        return new static($title);
    }

    public function view(): string
    {
        return <<<HTML
            <tr class="heading">
                <td colspan="2">
                    <b>$this->title</b>
                </td>
            </tr>
        HTML;
    }
}
