<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Tests\Fields;

use Gelion\BitrixOptions\Fields\HtmlEditor;
use PHPUnit\Framework\TestCase;

final class HtmlEditorTest extends TestCase
{
    protected function tearDown(): void
    {
        \Bitrix\Main\Config\Option::reset();
        \Bitrix\Main\Application::reset();
    }

    public function test_make_creates_instance(): void
    {
        $field = HtmlEditor::make('test_key');

        $this->assertInstanceOf(HtmlEditor::class, $field);
    }

    public function test_make_sets_key_and_title(): void
    {
        $field = HtmlEditor::make('test_key', 'Test Title');

        $this->assertSame('test_key', $field->key);
        $this->assertSame('Test Title', $field->title);
    }

    public function test_fluent_interface(): void
    {
        $field = HtmlEditor::make('test_key');

        $this->assertInstanceOf(HtmlEditor::class, $field->setModuleId('test.module'));
        $this->assertInstanceOf(HtmlEditor::class, $field->setHint('hint text'));
    }

    public function test_view_returns_html_with_editor_container(): void
    {
        $field = HtmlEditor::make('test_key', 'Test Title');
        $field->setModuleId('test.module');
        \Bitrix\Main\Config\Option::set('test.module', 'test_key', serialize(['type' => 'HTML', 'value' => '<p>test</p>']));
        $field->fillValue();

        $html = $field->view();

        $this->assertStringContainsString('<tr>', $html);
        $this->assertStringContainsString('test_key-block', $html);
        $this->assertStringContainsString('test_key-container', $html);
    }

    public function test_view_contains_escaped_title(): void
    {
        $field = HtmlEditor::make('test_key', '<script>alert("xss")</script>');
        $field->setModuleId('test.module');
        \Bitrix\Main\Config\Option::set('test.module', 'test_key', serialize(['type' => 'HTML', 'value' => '']));
        $field->fillValue();

        $html = $field->view();

        $this->assertStringContainsString('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_view_contains_escaped_key(): void
    {
        $field = HtmlEditor::make('<script>alert("xss")</script>', 'Test');
        $field->setModuleId('test.module');
        \Bitrix\Main\Config\Option::set('test.module', '<script>alert("xss")</script>', serialize(['type' => 'HTML', 'value' => '']));
        $field->fillValue();

        $html = $field->view();

        $this->assertStringContainsString('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', $html);
    }

    public function test_fillValue_with_serialized_data(): void
    {
        \Bitrix\Main\Config\Option::set('test.module', 'test_key', serialize(['type' => 'HTML', 'value' => '<p>test</p>']));

        $field = HtmlEditor::make('test_key');
        $field->setModuleId('test.module');
        $field->fillValue();

        $this->assertIsArray($field->value);
        $this->assertArrayHasKey('type', $field->value);
        $this->assertArrayHasKey('value', $field->value);
        $this->assertSame('HTML', $field->value['type']);
        $this->assertSame('<p>test</p>', $field->value['value']);
    }

    public function test_fillValue_with_empty_string_returns_default(): void
    {
        \Bitrix\Main\Config\Option::set('test.module', 'test_key', '');

        $field = HtmlEditor::make('test_key');
        $field->setModuleId('test.module');
        $field->fillValue();

        $this->assertIsArray($field->value);
        $this->assertSame('TEXT', $field->value['type']);
        $this->assertSame('', $field->value['value']);
    }

    public function test_fillValue_with_serialized_false_returns_default(): void
    {
        \Bitrix\Main\Config\Option::set('test.module', 'test_key', serialize(false));

        $field = HtmlEditor::make('test_key');
        $field->setModuleId('test.module');
        $field->fillValue();

        $this->assertIsArray($field->value);
        $this->assertSame('TEXT', $field->value['type']);
        $this->assertSame('', $field->value['value']);
    }

    public function test_saveFromRequest_saves_serialized(): void
    {
        $field = HtmlEditor::make('HTML_KEY', 'Test');
        $field->setModuleId('test.module');

        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $request->setParam('update', 'Y');
        $request->setParam('mid', 'test.module');
        $request->setParam('HTML_KEY', json_encode(['type' => 'TEXT', 'value' => 'test']));

        $field->saveFromRequest();

        $saved = \Bitrix\Main\Config\Option::get('test.module', 'HTML_KEY');
        $this->assertNotEmpty($saved);
        $unserialized = @unserialize($saved);
        $this->assertIsArray($unserialized);
        $this->assertSame('TEXT', $unserialized['type']);
    }
}
