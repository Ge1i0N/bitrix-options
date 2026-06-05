<?php

declare(strict_types=1);

// Bitrix CMS stubs required by Form.php top-level code
// Extracted from XssTest.php to avoid redeclaration errors across tests

namespace Bitrix\Main\UI {

    class Extension
    {
        public static function load(string $ext): void {}
    }
}

namespace Bitrix\Main\Localization {

    class Loc
    {
        public static function getMessage(string $code): string
        {
            return $code;
        }

        public static function loadMessages(string $path): void {}
    }
}
