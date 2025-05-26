<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Enum;

enum Color: string
{
    case ACTIVE   = 'ui-ctl-active';
    case HOVER    = 'ui-ctl-hover';
    case SUCCESS  = 'ui-ctl-success';
    case WARNING  = 'ui-ctl-warning';
    case DANGER   = 'ui-ctl-danger';
    case DISABLED = 'ui-ctl-disabled';

    public function toString(): ?string
    {
        return $this->value;
    }
}
