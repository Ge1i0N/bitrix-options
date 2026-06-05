<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Tests\Fields;

use Gelion\BitrixOptions\Fields\Text;
use Gelion\BitrixOptions\Enums\Color;
use Gelion\BitrixOptions\Enums\Display;
use Gelion\BitrixOptions\Enums\Height;
use Gelion\BitrixOptions\Enums\TextStyle;
use Gelion\BitrixOptions\Enums\Width;
use PHPUnit\Framework\TestCase;

final class TextTest extends TestCase
{
    protected function tearDown(): void
    {
        \Bitrix\Main\Application::reset();
        \Bitrix\Main\Config\Option::reset();
    }

    public function test_make_creates_instance(): void
    {
        $field = Text::make('test_key');

        $this->assertInstanceOf(Text::class, $field);
    }

    public function test_make_sets_key_and_title(): void
    {
        $field = Text::make('test_key', 'Test Title');

        $this->assertSame('test_key', $field->key);
        $this->assertSame('Test Title', $field->title);
    }

    public function test_make_uses_key_as_default_title(): void
    {
        $field = Text::make('test_key');

        $this->assertSame('test_key', $field->title);
    }

    public function test_fluent_interface_returns_self(): void
    {
        $field = Text::make('test_key');

        $this->assertInstanceOf(Text::class, $field->setPlaceholder('Enter value'));
        $this->assertInstanceOf(Text::class, $field->setDefault('default'));
        $this->assertInstanceOf(Text::class, $field->setDisplay(Display::BLOCK));
        $this->assertInstanceOf(Text::class, $field->setWidth(Width::W50));
        $this->assertInstanceOf(Text::class, $field->setHeight(Height::MD));
        $this->assertInstanceOf(Text::class, $field->setStyle(TextStyle::ROUND));
        $this->assertInstanceOf(Text::class, $field->setColor(Color::SUCCESS));
        $this->assertInstanceOf(Text::class, $field->setHint('hint text'));
        $this->assertInstanceOf(Text::class, $field->setAutocomplete('on'));
        $this->assertInstanceOf(Text::class, $field->disabled());
        $this->assertInstanceOf(Text::class, $field->readonly());
        $this->assertInstanceOf(Text::class, $field->setModuleId('test.module'));
    }

    public function test_setters_store_values(): void
    {
        $field = Text::make('test_key')
            ->setPlaceholder('Enter value')
            ->setDefault('default_val')
            ->setDisplay(Display::BLOCK)
            ->setWidth(Width::W50)
            ->setHeight(Height::MD)
            ->setStyle(TextStyle::ROUND)
            ->setColor(Color::SUCCESS)
            ->setHint('hint text')
            ->setAutocomplete('on')
            ->disabled()
            ->readonly()
            ->setModuleId('test.module');

        $this->assertSame('Enter value', $field->placeholder);
        $this->assertSame('default_val', $field->default);
        $this->assertSame(Display::BLOCK, $field->display);
        $this->assertSame(Width::W50, $field->width);
        $this->assertSame(Height::MD, $field->height);
        $this->assertSame(TextStyle::ROUND, $field->style);
        $this->assertSame(Color::SUCCESS, $field->color);
        $this->assertSame('hint text', $field->hint);
        $this->assertSame('on', $field->autocomplete);
        $this->assertTrue($field->disabled);
        $this->assertTrue($field->readonly);
        $this->assertSame('test.module', $field->moduleId);
    }

    public function test_view_returns_html_string(): void
    {
        $field = Text::make('test_key', 'Test Title');

        $html = $field->view();

        $this->assertNotEmpty($html);
        $this->assertIsString($html);
    }

    public function test_view_contains_key_in_html(): void
    {
        $field = Text::make('test_key', 'Test Title');

        $html = $field->view();

        $this->assertStringContainsString('test_key', $html);
    }

    public function test_view_contains_title(): void
    {
        $field = Text::make('test_key', 'Test Title');

        $html = $field->view();

        $this->assertStringContainsString('Test Title', $html);
    }

    public function test_view_contains_input_with_text_type(): void
    {
        $field = Text::make('test_key');

        $html = $field->view();

        $this->assertStringContainsString('type="text"', $html);
    }

    public function test_view_contains_disabled_when_set(): void
    {
        $field = Text::make('test_key');
        $field->disabled();

        $html = $field->view();

        $this->assertStringContainsString('disabled', $html);
    }

    public function test_view_contains_readonly_when_set(): void
    {
        $field = Text::make('test_key');
        $field->readonly();

        $html = $field->view();

        $this->assertStringContainsString('readonly', $html);
    }

    public function test_fillValue_gets_from_option(): void
    {
        \Bitrix\Main\Config\Option::set('test.module', 'test_key', 'stored_value');

        $field = Text::make('test_key', 'Test Title');
        $field->setModuleId('test.module');
        $field->fillValue();

        $this->assertSame('stored_value', $field->value);
    }

    public function test_fillValue_uses_default_when_no_option(): void
    {
        $field = Text::make('test_key', 'Test Title');
        $field->setModuleId('test.module');
        $field->setDefault('default_value');
        $field->fillValue();

        $this->assertSame('default_value', $field->value);
    }
}
