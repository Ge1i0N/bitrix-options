<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Tests\Fields;

use Gelion\BitrixOptions\Fields\Textarea;
use Gelion\BitrixOptions\Enums\Resize;
use PHPUnit\Framework\TestCase;

final class TextareaTest extends TestCase
{
    protected function tearDown(): void
    {
        \Bitrix\Main\Application::reset();
        \Bitrix\Main\Config\Option::reset();
    }

    public function test_make_creates_instance(): void
    {
        $field = Textarea::make('test_key');

        $this->assertInstanceOf(Textarea::class, $field);
    }

    public function test_make_sets_key_and_title(): void
    {
        $field = Textarea::make('test_key', 'Test Title');

        $this->assertSame('test_key', $field->key);
        $this->assertSame('Test Title', $field->title);
    }

    public function test_cols_method(): void
    {
        $field = Textarea::make('test_key');
        $field->cols(80);

        $this->assertSame(80, $field->cols);
    }

    public function test_rows_method(): void
    {
        $field = Textarea::make('test_key');
        $field->rows(10);

        $this->assertSame(10, $field->rows);
    }

    public function test_setResize(): void
    {
        $field = Textarea::make('test_key');
        $field->setResize(Resize::RESIZE_X);

        $this->assertSame(Resize::RESIZE_X, $field->resize);
    }

    public function test_fluent_interface_returns_self(): void
    {
        $field = Textarea::make('test_key');

        $this->assertInstanceOf(Textarea::class, $field->setModuleId('test.module'));
        $this->assertInstanceOf(Textarea::class, $field->setHint('hint text'));
        $this->assertInstanceOf(Textarea::class, $field->cols(80));
        $this->assertInstanceOf(Textarea::class, $field->rows(10));
        $this->assertInstanceOf(Textarea::class, $field->setResize(Resize::RESIZE_Y));
    }

    public function test_view_returns_html_with_textarea(): void
    {
        $field = Textarea::make('test_key', 'Test Title');
        $field->setModuleId('test.module');

        $html = $field->view();

        $this->assertStringContainsString('<textarea', $html);
    }

    public function test_view_contains_key(): void
    {
        $field = Textarea::make('test_key', 'Test Title');
        $field->setModuleId('test.module');

        $html = $field->view();

        $this->assertStringContainsString('name="test_key"', $html);
    }

    public function test_view_contains_placeholder(): void
    {
        $field = Textarea::make('test_key', 'Test Title');
        $field->setModuleId('test.module');
        $field->setPlaceholder('Enter your text here');

        $html = $field->view();

        $this->assertStringContainsString('placeholder="Enter your text here"', $html);
    }

    public function test_view_contains_rows_when_set(): void
    {
        $field = Textarea::make('test_key', 'Test Title');
        $field->setModuleId('test.module');
        $field->rows(10);

        $html = $field->view();

        $this->assertStringContainsString('rows="10"', $html);
    }

    public function test_view_contains_cols_when_set(): void
    {
        $field = Textarea::make('test_key', 'Test Title');
        $field->setModuleId('test.module');
        $field->cols(80);

        $html = $field->view();

        $this->assertStringContainsString('cols="80"', $html);
    }

    public function test_view_contains_resize_class(): void
    {
        $field = Textarea::make('test_key', 'Test Title');
        $field->setModuleId('test.module');
        $field->setResize(Resize::RESIZE_X);

        $html = $field->view();

        $this->assertStringContainsString('ui-ctl-resize-x', $html);
    }

    public function test_fillValue_gets_from_option(): void
    {
        \Bitrix\Main\Config\Option::set('test.module', 'test_key', 'stored_value');

        $field = Textarea::make('test_key', 'Test Title');
        $field->setModuleId('test.module');
        $field->fillValue();

        $this->assertSame('stored_value', $field->value);
    }

    public function test_fillValue_uses_default(): void
    {
        $field = Textarea::make('test_key', 'Test Title');
        $field->setModuleId('test.module');
        $field->setDefault('default_value');
        $field->fillValue();

        $this->assertSame('default_value', $field->value);
    }
}
