<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Traits;

trait WithStyleClass
{
    protected function getClassFromProps(): string
    {
        $result = '';

        foreach ($this->getStyleClassProperties() as $prop) {
            $result .= $this->{$prop}?->value ?? '';
        }

        return $result;
    }

    protected function getStyleClassProperties(): array
    {
        return [];
    }
}
