<?php

declare(strict_types=1);

/**
 * PHPStan-only stubs for Bitrix CMS classes used in src/.
 * Not loaded during tests — only parsed by PHPStan via scanDirectories.
 *
 * No class_exists guards because PHPStan's scanner needs clean definitions.
 */
namespace Bitrix\Main\Localization {
    class Loc
    {
        /** @param string $path */
        public static function loadMessages(string $path): void {}

        /**
         * @param string $code
         * @param array<string, mixed> $replace
         * @param string|null $language
         * @return string|null
         */
        public static function getMessage(string $code, array $replace = [], ?string $language = null): ?string
        {
            return null;
        }
    }
}

namespace Bitrix\Main\UI {
    class Extension
    {
        /** @param string $name */
        public static function load(string $name): void {}
    }
}

class CCatalogCondTree
{
    /**
     * @param int $mode
     * @param int $build
     * @param array<string, mixed> $params
     * @return bool
     */
    public function Init(int $mode, int $build, array $params = []): bool
    {
        return true;
    }

    /** @param mixed $value */
    public function Show($value): void {}

    /** @return array<string, mixed> */
    public function Parse(): array
    {
        return [];
    }
}

class CMain
{
    /** @return string */
    public function GetCurPage(): string
    {
        return '';
    }
}

define('BT_COND_MODE_DEFAULT', 0);
define('BT_COND_BUILD_CATALOG', 1);
define('BT_COND_MODE_PARSE', 2);
define('LANGUAGE_ID', 'en');
