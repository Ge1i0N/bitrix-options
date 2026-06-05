<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Gelion\BitrixOptionsMigration\Migrator;

/**
 * Пример 1: Простая конфигурация с одной вкладкой.
 *
 * Показывает самый простой сценарий миграции: одна вкладка, одна группа, базовые поля.
 */
$v1Config = [
    'module_id' => 'demo.settings',
    'tabs' => [
        [
            'DIV' => 'main',
            'TAB' => 'Main Settings',
            'GROUPS' => [
                [
                    'TITLE' => 'Basic',
                    'OPTIONS' => [
                        'SITE_NAME' => [
                            'TYPE' => 'STRING',
                            'TITLE' => 'Site Name',
                            'DEFAULT' => 'My Site',
                        ],
                        'SHOW_LOGO' => [
                            'TYPE' => 'CHECKBOX',
                            'TITLE' => 'Show Logo',
                        ],
                    ],
                ],
            ],
        ],
    ],
];

$migrator = new Migrator();
$result = $migrator->convert($v1Config);

$block = function (string $title, string $body) {
    $pad = str_repeat('#', 52);
    return "\n{$pad}\n  {$title}\n{$pad}\n\n{$body}\n";
};

$warnings = $result->hasWarnings()
    ? implode('', array_map(
        fn(array $w) => "  [{$w['type']}] {$w['message']}\n",
        $result->warnings
    ))
    : "  Нет предупреждений\n";

echo $block('ПРЕДУПРЕЖДЕНИЯ', $warnings);

$outputPath = __DIR__ . '/output-simple.php';
file_put_contents($outputPath, $result->code);

echo $block('РЕЗУЛЬТАТ',
    "  Успех: " . ($result->success ? 'Да' : 'Нет') . "\n"
    . "\n"
    . "  Файл: {$outputPath}\n"
    . "\n"
    . "  PHP lint: " . trim(shell_exec('php -l ' . escapeshellarg($outputPath) . ' 2>&1'))
);
