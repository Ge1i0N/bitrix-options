<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Enum;

enum AlertSize: string
{
    case MD = 'ui-alert-md';
    case XS = 'ui-alert-xs';

    public function toString(): ?string
    {
        return $this->value;
    }
}
