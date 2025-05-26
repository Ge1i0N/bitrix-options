<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Traits;

trait WithDisplayWithoutValue
{
    public function fillValue(): static
    {
        return $this;
    }

    public function saveFromRequest(): static
    {
        return $this;
    }
}
