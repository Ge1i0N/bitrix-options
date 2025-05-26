<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Enum;

enum AlertIcon: string
{
    case WARNING = 'ui-alert-icon-warning';
    case DANGER  = 'ui-alert-icon-danger';
    case INFO    = 'ui-alert-icon-info';

    public function toString(): ?string
    {
        return $this->value;
    }
}
