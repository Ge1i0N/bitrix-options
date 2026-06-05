<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Tests\Fields;

use Gelion\BitrixOptions\Fields\Checkbox;
use PHPUnit\Framework\TestCase;

final class CheckboxTest extends TestCase
{
    protected function tearDown(): void
    {
        \Bitrix\Main\Config\Option::reset();
    }

    public function test_make_creates_instance(): void
    {
        $field = Checkbox::make('test_key', 'Test Title');

        $this->assertInstanceOf(Checkbox::class, $field);
    }

    public function test_view_contains_checkbox_input(): void
    {
        $field = Checkbox::make('test_key');

        $html = $field->view();

        $this->assertStringContainsString('type="checkbox"', $html);
        $this->assertStringContainsString('<input', $html);
    }

    public function test_checked_when_value_is_y(): void
    {
        \Bitrix\Main\Config\Option::set('test.module', 'test_key', 'Y');

        $field = Checkbox::make('test_key');
        $field->setModuleId('test.module');
        $field->fillValue();

        $html = $field->view();

        $this->assertStringContainsString('checked', $html);
    }

    public function test_not_checked_when_value_is_n(): void
    {
        \Bitrix\Main\Config\Option::set('test.module', 'test_key', 'N');

        $field = Checkbox::make('test_key');
        $field->setModuleId('test.module');
        $field->fillValue();

        $html = $field->view();

        $this->assertStringNotContainsString('checked', $html);
    }

    public function test_fluent_interface(): void
    {
        $field = Checkbox::make('test_key');

        $this->assertInstanceOf(Checkbox::class, $field->setModuleId('test.module'));
        $this->assertInstanceOf(Checkbox::class, $field->setHint('hint text'));
    }
}
