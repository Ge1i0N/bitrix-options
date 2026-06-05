<?php

declare(strict_types=1);

namespace Bitrix\Main\Config;

/**
 * Mock for Bitrix\Main\Config\Option.
 * Stores options in-memory instead of the database.
 */
class Option
{
    private static array $storage = [];

    public static function get(string $moduleId, string $key, mixed $default = ''): string
    {
        return (string) (self::$storage[$moduleId][$key] ?? $default);
    }

    public static function set(string $moduleId, string $key, string $value): void
    {
        self::$storage[$moduleId][$key] = $value;
    }

    /**
     * Resets all stored options. Use in test tear-down methods.
     */
    public static function reset(): void
    {
        self::$storage = [];
    }
}
