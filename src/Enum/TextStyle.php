<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Enum;

enum TextStyle: string
{
    case NO_BORDER  = 'ui-ctl-no-border';
    case NO_PADDING = 'ui-ctl-no-padding';
    case ROUND      = 'ui-ctl-round';
    case UNDERLINE  = 'ui-ctl-underline';

    public function toString(): ?string
    {
        return $this->value;
    }
}
