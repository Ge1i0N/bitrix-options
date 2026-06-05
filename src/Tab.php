<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions;

use Gelion\BitrixOptions\Fields\Text;

/** @phpstan-consistent-constructor */
class Tab
{
    /** @param Text[] $fields */
    public function __construct(
        public string $id,
        public string $title,
        public array $fields,
    ) {
    }

    /** @param Text[] $fields */
    public static function make(string $id, string $title, array $fields): static
    {
        return new static($id, $title, $fields);
    }

    public function view(string $moduleId): string
    {
        $result = '';

        foreach ($this->fields as $field) {
            $result .= $field
                ->setModuleId($moduleId)
                ->saveFromRequest()
                ->fillValue()
                ->view();
        }

        return $result;
    }
}
