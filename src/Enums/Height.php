<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Enums;

enum Height: string
{
    case LG = 'ui-ctl-lg';
    case MD = 'ui-ctl-md';
    case SM = 'ui-ctl-sm';
    case XS = 'ui-ctl-xs';

    public function toString(): ?string
    {
        return $this->value;
    }
}
