<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Gelion\BitrixOptionsMigration\Migrator;

/**
 * Пример 2: Сложная конфигурация с несколькими вкладками, группами и всеми параметрами.
 *
 * Демонстрирует: несколько вкладок, группы с заголовками и без, NUMBER с min/max/step,
 * TEXTAREA с cols/rows, DROPDOWN/MULTISELECT с опциями, ALERT, TAG+TAGCOLOR.
 */
$v1Config = [
    'module_id' => 'demo.complex',
    'tabs' => [
        [
            'DIV' => 'main',
            'TAB' => 'Main',
            'GROUPS' => [
                [
                    'TITLE' => 'Configuration',
                    'OPTIONS' => [
                        'SITE_NAME' => [
                            'TYPE' => 'STRING',
                            'TITLE' => 'Site Name',
                            'DEFAULT' => 'My App',
                            'NOTES' => 'Shown in browser title',
                            'PLACEHOLDER' => 'Enter site name',
                            'AUTOCOMPLETE' => 'off',
                            'TAG' => 'required',
                            'TAGCOLOR' => 'danger',
                            'SORT' => 100,
                        ],
                        'MAX_ITEMS' => [
                            'TYPE' => 'NUMBER',
                            'TITLE' => 'Max Items',
                            'DEFAULT' => '50',
                            'STEP' => 5,
                            'MIN' => 0,
                            'MAX' => 500,
                            'SORT' => 200,
                        ],
                        'DESCRIPTION' => [
                            'TYPE' => 'TEXTAREA',
                            'TITLE' => 'Description',
                            'COLS' => 80,
                            'ROWS' => 6,
                            'SORT' => 300,
                        ],
                        'THEME' => [
                            'TYPE' => 'DROPDOWN',
                            'TITLE' => 'Theme',
                            'OPTIONS' => [
                                ['TITLE' => 'Light', 'VALUE' => 'light'],
                                ['TITLE' => 'Dark', 'VALUE' => 'dark'],
                                ['TITLE' => 'Auto', 'VALUE' => 'auto'],
                            ],
                            'SORT' => 400,
                        ],
                        'COLORS' => [
                            'TYPE' => 'MULTISELECT',
                            'TITLE' => 'Colors',
                            'SIZE' => 4,
                            'OPTIONS' => [
                                ['TITLE' => 'Red', 'VALUE' => 'red'],
                                ['TITLE' => 'Green', 'VALUE' => 'green'],
                                ['TITLE' => 'Blue', 'VALUE' => 'blue'],
                            ],
                            'SORT' => 500,
                        ],
                    ],
                ],
            ],
        ],
        [
            'DIV' => 'advanced',
            'TAB' => 'Advanced',
            'GROUPS' => [
                [
                    // Группа без заголовка — заголовок не создаётся
                    'OPTIONS' => [
                        'DEBUG_MODE' => ['TYPE' => 'CHECKBOX', 'TITLE' => 'Debug Mode'],
                        'ALERT_MSG' => ['TYPE' => 'ALERT', 'TITLE' => 'Changes may affect performance'],
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

$outputPath = __DIR__ . '/output-complex.php';
file_put_contents($outputPath, $result->code);

echo $block('РЕЗУЛЬТАТ',
    "  Успех: " . ($result->success ? 'Да' : 'Нет') . "\n"
    . "\n"
    . "  Файл: {$outputPath}\n"
    . "\n"
    . "  PHP lint: " . trim(shell_exec('php -l ' . escapeshellarg($outputPath) . ' 2>&1'))
);
