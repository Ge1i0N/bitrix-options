<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Enums;

enum Width: string
{
    case W25     = 'ui-ctl-w25';
    case W33     = 'ui-ctl-w33';
    case W50     = 'ui-ctl-w50';
    case W75     = 'ui-ctl-w75';
    case FULL    = 'ui-ctl-w100';
    case AUTO    = 'ui-ctl-wa';
    case DEFAULT = 'ui-ctl-wd';

    public function toString(): ?string
    {
        return $this->value;
    }
}
