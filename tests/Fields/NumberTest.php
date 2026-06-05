<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Tests\Fields;

use Gelion\BitrixOptions\Fields\Number;
use PHPUnit\Framework\TestCase;

final class NumberTest extends TestCase
{
    public function test_make_creates_instance(): void
    {
        $field = Number::make('test_key', 'Test Title');

        $this->assertInstanceOf(Number::class, $field);
    }

    public function test_min_max_step_setters(): void
    {
        $field = Number::make('test_key');

        $resultMin = $field->min(0);
        $resultMax = $field->max(100);
        $resultStep = $field->step(5);

        $this->assertSame('0', $field->min);
        $this->assertSame('100', $field->max);
        $this->assertSame('5', $field->step);

        $this->assertInstanceOf(Number::class, $resultMin);
        $this->assertInstanceOf(Number::class, $resultMax);
        $this->assertInstanceOf(Number::class, $resultStep);
    }

    public function test_view_contains_number_input(): void
    {
        $field = Number::make('test_key');

        $html = $field->view();

        $this->assertStringContainsString('type="number"', $html);
    }

    public function test_view_contains_min_max_step_attributes(): void
    {
        $field = Number::make('test_key');
        $field->min(0);
        $field->max(100);
        $field->step(5);

        $html = $field->view();

        $this->assertStringContainsString('min="0"', $html);
        $this->assertStringContainsString('max="100"', $html);
        $this->assertStringContainsString('step="5"', $html);
    }

    public function test_view_omits_min_when_not_set(): void
    {
        $field = Number::make('test_key');

        $html = $field->view();

        $this->assertStringNotContainsString('min="', $html);
    }

    public function test_fluent_interface(): void
    {
        $field = Number::make('test_key');

        $this->assertInstanceOf(Number::class, $field->min(0));
        $this->assertInstanceOf(Number::class, $field->max(100));
        $this->assertInstanceOf(Number::class, $field->step(1));
    }
}
