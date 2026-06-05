<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Tests\Migrator;

use Gelion\BitrixOptionsMigration\Migrator;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function createMinimalConfig(): array
    {
        return [
            'module_id' => 'test.module',
            'tabs' => [
                [
                    'DIV' => 'tab1',
                    'TAB' => 'Tab 1',
                    'GROUPS' => [
                        [
                            'TITLE' => 'Group 1',
                            'OPTIONS' => [
                                'MY_OPTION' => ['TYPE' => 'STRING', 'TITLE' => 'My Option'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function createMigrator(): \Gelion\BitrixOptionsMigration\Migrator
    {
        return new \Gelion\BitrixOptionsMigration\Migrator();
    }

    protected function assertGeneratedCodeContains(string $needle, string $code): void
    {
        $this->assertStringContainsString($needle, $code);
    }

    protected function assertGeneratedCodeNotContains(string $needle, string $code): void
    {
        $this->assertStringNotContainsString($needle, $code);
    }
}
