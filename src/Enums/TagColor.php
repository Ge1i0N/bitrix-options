<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Enums;

enum TagColor: string
{
    case DEFAULT = 'ui-ctl-tag';
    case SUCCESS = 'ui-ctl-tag-success';
    case PRIMARY = 'ui-ctl-tag-primary';
    case DANGER  = 'ui-ctl-tag-danger';
    case WARNING = 'ui-ctl-tag-warning';
}
