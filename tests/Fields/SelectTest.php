<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Tests\Fields;

use Gelion\BitrixOptions\Fields\Select;
use PHPUnit\Framework\TestCase;

final class SelectTest extends TestCase
{
    protected function tearDown(): void
    {
        \Bitrix\Main\Application::reset();
        \Bitrix\Main\Config\Option::reset();
    }

    public function test_make_creates_instance(): void
    {
        $field = Select::make('test_key', 'Test Title');

        $this->assertInstanceOf(Select::class, $field);
    }

    public function test_options_method_stores_options(): void
    {
        $options = [
            'value1' => 'Label 1',
            'value2' => 'Label 2',
            'value3' => 'Label 3',
        ];

        $field = Select::make('test_key');
        $field->options($options);

        $this->assertSame($options, $field->options);
    }

    public function test_view_returns_html_with_select_tag(): void
    {
        $field = Select::make('test_key');

        $html = $field->view();

        $this->assertStringContainsString('<select', $html);
        $this->assertStringContainsString('</select>', $html);
    }

    public function test_view_contains_options(): void
    {
        $field = Select::make('test_key');
        $field->options([
            'opt1' => 'Option 1',
            'opt2' => 'Option 2',
        ]);

        $html = $field->view();

        $this->assertStringContainsString('Option 1', $html);
        $this->assertStringContainsString('Option 2', $html);
        $this->assertStringContainsString('value="opt1"', $html);
        $this->assertStringContainsString('value="opt2"', $html);
    }

    public function test_fluent_interface(): void
    {
        $field = Select::make('test_key');

        $this->assertInstanceOf(Select::class, $field->options(['key' => 'val']));
        $this->assertInstanceOf(Select::class, $field->multiple());
        $this->assertInstanceOf(Select::class, $field->size(5));
    }

    public function test_multiple_attribute_appears_when_set(): void
    {
        $field = Select::make('test_key');
        $field->multiple();
        $field->options(['a' => 'A']);

        $html = $field->view();

        $this->assertStringContainsString('multiple', $html);
        $this->assertStringContainsString('name="test_key[]"', $html);
    }

    public function test_size_attribute_appears_when_set(): void
    {
        $field = Select::make('test_key');
        $field->size(10);
        $field->options(['a' => 'A']);

        $html = $field->view();

        $this->assertStringContainsString('size="10"', $html);
    }

    public function test_fillValue_gets_from_option(): void
    {
        \Bitrix\Main\Config\Option::set('test.module', 'test_key', 'selected_value');

        $field = Select::make('test_key');
        $field->setModuleId('test.module');
        $field->fillValue();

        $this->assertSame('selected_value', $field->value);
    }

    public function test_selected_option_is_marked(): void
    {
        \Bitrix\Main\Config\Option::set('test.module', 'test_key', 'opt2');

        $field = Select::make('test_key');
        $field->setModuleId('test.module');
        $field->fillValue();
        $field->options([
            'opt1' => 'Option 1',
            'opt2' => 'Option 2',
            'opt3' => 'Option 3',
        ]);

        $html = $field->view();

        $this->assertStringContainsString('value="opt2" selected', $html);
    }
}
