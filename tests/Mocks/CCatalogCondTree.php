<?php

declare(strict_types=1);

// Mock for CCatalogCondTree used by Condition.php view() and saveFromRequest()

define('BT_COND_MODE_DEFAULT', 'DEFAULT');
define('BT_COND_BUILD_CATALOG', 'CATALOG');
define('BT_COND_MODE_PARSE', 'PARSE');

class CCatalogCondTree
{
    public ?string $lastMode = null;
    public array $lastParams = [];

    public function Init(string $mode, string $build, array $params = []): bool
    {
        $this->lastMode = $mode;
        $this->lastParams = $params;
        return true;
    }

    public function Show($value): void
    {
        echo '<div class="condition-tree-mock">' . htmlspecialchars(is_string($value) ? $value : '') . '</div>';
    }

    public function Parse(): array
    {
        return ['CLASS_ID' => 'CondGroup', 'DATA' => []];
    }
}
