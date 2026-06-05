<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Traits;

trait WithDisplayWithoutValue
{
    private bool $htmlAllowed = false;

    public function allowHtml(): static
    {
        $this->htmlAllowed = true;

        return $this;
    }

    protected function isHtmlAllowed(): bool
    {
        return $this->htmlAllowed;
    }

    public function fillValue(): static
    {
        return $this;
    }

    public function saveFromRequest(): static
    {
        return $this;
    }
}
