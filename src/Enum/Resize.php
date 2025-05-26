<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Enum;

enum Resize: string
{
    case NO_RESIZE = 'ui-ctl-no-resize';
    case RESIZE_Y  = 'ui-ctl-resize-y';
    case RESIZE_X  = 'ui-ctl-resize-x';

    public function toString(): ?string
    {
        return $this->value;
    }
}
