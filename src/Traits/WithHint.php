<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Traits;

trait WithHint
{
    public string $hint = '';

    public function setHint($hint = ''): static
    {
        $this->hint = (string) $hint;

        return $this;
    }

    protected function getHintView(): string
    {
        return mb_strlen(trim($this->hint)) > 0
            ? sprintf('<span data-hint="%s"></span>', htmlspecialchars($this->hint))
            : '';
    }
}
