<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions;

class Tab
{
    public function __construct(
        public string $id,
        public string $title,
        public array $fields,
    ) {
    }

    public static function make($id, $title, $fields): static
    {
        return new static($id, $title, $fields);
    }

    public function view($moduleId): string
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
