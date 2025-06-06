<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Enums;

enum Resize: string
{
    case NO_RESIZE = 'ui-ctl-no-resize';
    case RESIZE_Y  = 'ui-ctl-resize-y';
    case RESIZE_X  = 'ui-ctl-resize-x';
}
