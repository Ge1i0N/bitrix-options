<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Traits;

trait WithModuleId
{
    public string $moduleId;

    public function setModuleId(string $moduleId): static
    {
        $this->moduleId = $moduleId;

        return $this;
    }
}
