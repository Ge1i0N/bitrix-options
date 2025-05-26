<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Enum;

enum AlertTextPosition: string
{
    case CENTER = 'ui-alert-text-center';
    case INLINE = 'ui-alert-text-inline';

    public function toString(): ?string
    {
        return $this->value;
    }
}
