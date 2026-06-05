<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Gelion\BitrixOptionsMigration\Migrator;

/**
 * Пример 3: Все 10 типов полей.
 *
 * Демонстрирует все встроенные типы полей v1 и их сопоставление с классами v2.
 */
$v1Config = [
    'module_id' => 'demo.alltypes',
    'tabs' => [
        [
            'DIV' => 'fields',
            'TAB' => 'All Fields',
            'OPTIONS' => [
                'TEXT_FIELD' => ['TYPE' => 'STRING', 'TITLE' => 'Text'],
                'NUM_FIELD' => ['TYPE' => 'NUMBER', 'TITLE' => 'Number'],
                'CHK_FIELD' => ['TYPE' => 'CHECKBOX', 'TITLE' => 'Checkbox'],
                'TAR_FIELD' => ['TYPE' => 'TEXTAREA', 'TITLE' => 'Textarea'],
                'CLR_FIELD' => ['TYPE' => 'COLORPICKER', 'TITLE' => 'Color'],
                'DRP_FIELD' => ['TYPE' => 'DROPDOWN', 'TITLE' => 'Dropdown'],
                'MUL_FIELD' => ['TYPE' => 'MULTISELECT', 'TITLE' => 'Multi'],
                'HTML_FIELD' => ['TYPE' => 'HTMLEDITOR', 'TITLE' => 'Html Editor'],
                'CON_FIELD' => ['TYPE' => 'CONDITIONS', 'TITLE' => 'Condition'],
                'ALERT_MSG' => ['TYPE' => 'ALERT', 'TITLE' => 'Info alert'],
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

$outputPath = __DIR__ . '/output-all-types.php';
file_put_contents($outputPath, $result->code);

echo $block('РЕЗУЛЬТАТ',
    "  Успех: " . ($result->success ? 'Да' : 'Нет') . "\n"
    . "\n"
    . "  Файл: {$outputPath}\n"
    . "\n"
    . "  PHP lint: " . trim(shell_exec('php -l ' . escapeshellarg($outputPath) . ' 2>&1'))
);

