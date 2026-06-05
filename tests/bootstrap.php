<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Bitrix CMS mocks for test isolation
require_once __DIR__ . '/Mocks/BitrixMocks.php';
require_once __DIR__ . '/Mocks/OptionMock.php';
require_once __DIR__ . '/Mocks/ApplicationMock.php';
require_once __DIR__ . '/Mocks/CAdminTabControl.php';
require_once __DIR__ . '/Mocks/CFileMan.php';
require_once __DIR__ . '/Mocks/FormDependencies.php';
require_once __DIR__ . '/Mocks/CCatalogCondTree.php';

if (!defined('LANGUAGE_ID')) {
    define('LANGUAGE_ID', 'ru');
}

$_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] ?? __DIR__ . '/../..';
