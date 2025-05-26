<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Enum;

enum Display: string
{
    case INLINE = 'ui-ctl-inline';
    case BLOCK  = 'ui-ctl-block';

    public function toString(): ?string
    {
        return $this->value;
    }
}
