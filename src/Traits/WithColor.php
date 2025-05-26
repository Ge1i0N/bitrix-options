<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Traits;

use Gelion\BitrixOptions\Enum\Color;

trait WithColor
{
    public ?Color $color = null;

    public function setColor(Color $color): static
    {
        $this->color = $color;

        return $this;
    }
}
