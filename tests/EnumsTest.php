<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Tests;

use Gelion\BitrixOptions\Enums\AlertIcon;
use Gelion\BitrixOptions\Enums\AlertSize;
use Gelion\BitrixOptions\Enums\AlertTextPosition;
use Gelion\BitrixOptions\Enums\Color;
use Gelion\BitrixOptions\Enums\Display;
use Gelion\BitrixOptions\Enums\Height;
use Gelion\BitrixOptions\Enums\Resize;
use Gelion\BitrixOptions\Enums\TagColor;
use Gelion\BitrixOptions\Enums\TextStyle;
use Gelion\BitrixOptions\Enums\Width;
use PHPUnit\Framework\TestCase;

final class EnumsTest extends TestCase
{
    /**
     * @return array<string, array{0: class-string, 1: int, 2: array<string, string>}>
     */
    public static function enumProvider(): array
    {
        return [
            'AlertIcon' => [
                AlertIcon::class,
                3,
                [
                    'WARNING' => 'ui-alert-icon-warning',
                    'DANGER' => 'ui-alert-icon-danger',
                    'INFO' => 'ui-alert-icon-info',
                ],
            ],
            'AlertSize' => [
                AlertSize::class,
                2,
                [
                    'MD' => 'ui-alert-md',
                    'XS' => 'ui-alert-xs',
                ],
            ],
            'AlertTextPosition' => [
                AlertTextPosition::class,
                2,
                [
                    'CENTER' => 'ui-alert-text-center',
                    'INLINE' => 'ui-alert-text-inline',
                ],
            ],
            'Color' => [
                Color::class,
                6,
                [
                    'ACTIVE' => 'ui-ctl-active',
                    'HOVER' => 'ui-ctl-hover',
                    'SUCCESS' => 'ui-ctl-success',
                    'WARNING' => 'ui-ctl-warning',
                    'DANGER' => 'ui-ctl-danger',
                    'DISABLED' => 'ui-ctl-disabled',
                ],
            ],
            'Display' => [
                Display::class,
                2,
                [
                    'INLINE' => 'ui-ctl-inline',
                    'BLOCK' => 'ui-ctl-block',
                ],
            ],
            'Height' => [
                Height::class,
                4,
                [
                    'LG' => 'ui-ctl-lg',
                    'MD' => 'ui-ctl-md',
                    'SM' => 'ui-ctl-sm',
                    'XS' => 'ui-ctl-xs',
                ],
            ],
            'Resize' => [
                Resize::class,
                3,
                [
                    'NO_RESIZE' => 'ui-ctl-no-resize',
                    'RESIZE_Y' => 'ui-ctl-resize-y',
                    'RESIZE_X' => 'ui-ctl-resize-x',
                ],
            ],
            'TagColor' => [
                TagColor::class,
                5,
                [
                    'DEFAULT' => 'ui-ctl-tag',
                    'SUCCESS' => 'ui-ctl-tag-success',
                    'PRIMARY' => 'ui-ctl-tag-primary',
                    'DANGER' => 'ui-ctl-tag-danger',
                    'WARNING' => 'ui-ctl-tag-warning',
                ],
            ],
            'TextStyle' => [
                TextStyle::class,
                4,
                [
                    'NO_BORDER' => 'ui-ctl-no-border',
                    'NO_PADDING' => 'ui-ctl-no-padding',
                    'ROUND' => 'ui-ctl-round',
                    'UNDERLINE' => 'ui-ctl-underline',
                ],
            ],
            'Width' => [
                Width::class,
                7,
                [
                    'W25' => 'ui-ctl-w25',
                    'W33' => 'ui-ctl-w33',
                    'W50' => 'ui-ctl-w50',
                    'W75' => 'ui-ctl-w75',
                    'FULL' => 'ui-ctl-w100',
                    'AUTO' => 'ui-ctl-wa',
                    'DEFAULT' => 'ui-ctl-wd',
                ],
            ],
        ];
    }

    /**
     * @param class-string $enumClass
     * @param int $expectedCount
     * @param array<string, string> $sampleMap
     *
     * @dataProvider enumProvider
     */
    public function test_enum_has_expected_case_count(
        string $enumClass,
        int $expectedCount,
        array $sampleMap
    ): void {
        $this->assertCount($expectedCount, $enumClass::cases());
    }

    /**
     * @param class-string $enumClass
     * @param int $expectedCount
     * @param array<string, string> $sampleMap
     *
     * @dataProvider enumProvider
     */
    public function test_enum_from_returns_correct_case(
        string $enumClass,
        int $expectedCount,
        array $sampleMap
    ): void {
        foreach ($sampleMap as $caseName => $value) {
            $this->assertSame(
                $enumClass::from($value),
                $enumClass::tryFrom($value),
                "{$enumClass}::from('{$value}') should match tryFrom()"
            );
        }
    }

    /**
     * @param class-string $enumClass
     * @param int $expectedCount
     * @param array<string, string> $sampleMap
     *
     * @dataProvider enumProvider
     */
    public function test_enum_tryFrom_returns_null_for_invalid(
        string $enumClass,
        int $expectedCount,
        array $sampleMap
    ): void {
        $this->assertNull($enumClass::tryFrom('invalid'));
    }
}
