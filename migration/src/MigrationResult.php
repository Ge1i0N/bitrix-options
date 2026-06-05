<?php

declare(strict_types=1);

namespace Gelion\BitrixOptionsMigration;

final class MigrationResult
{
    public function __construct(
        public readonly string $code = '',
        public readonly array $warnings = [],
        public readonly array $imports = [],
        public readonly bool $success = false,
    ) {}

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'warnings' => $this->warnings,
            'imports' => $this->imports,
            'success' => $this->success,
        ];
    }

    public function hasWarnings(): bool
    {
        return $this->warnings !== [];
    }
}
