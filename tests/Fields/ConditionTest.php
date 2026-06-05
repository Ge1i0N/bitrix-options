<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Tests\Fields;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Gelion\BitrixOptions\Fields\Condition;
use PHPUnit\Framework\TestCase;

final class ConditionTest extends TestCase
{
    protected function tearDown(): void
    {
        Application::reset();
        Option::reset();
    }

    public function test_make_creates_instance(): void
    {
        $field = Condition::make('COND_KEY');

        $this->assertInstanceOf(Condition::class, $field);
    }

    public function test_make_sets_key_and_title(): void
    {
        $field = Condition::make('COND_KEY', 'Test Title');

        $this->assertSame('COND_KEY', $field->key);
        $this->assertSame('Test Title', $field->title);
    }

    public function test_view_returns_html(): void
    {
        $field = Condition::make('COND_KEY');
        $field->setModuleId('test.module');
        $field->fillValue();

        $html = $field->view();

        $this->assertNotEmpty($html);
        $this->assertStringContainsString('condition', $html);
        $this->assertStringContainsString('<tr>', $html);
    }

    public function test_fillValue_with_empty_string_returns_empty_array(): void
    {
        Option::set('test.module', 'COND_KEY', '');

        $field = Condition::make('COND_KEY');
        $field->setModuleId('test.module');
        $field->fillValue();

        $this->assertSame([], $field->value);
    }

    public function test_fillValue_with_valid_base64_data(): void
    {
        $data = base64_encode(serialize(['CLASS_ID' => 'CondGroup']));
        Option::set('test.module', 'COND_KEY', $data);

        $field = Condition::make('COND_KEY');
        $field->setModuleId('test.module');
        $field->fillValue();

        $this->assertIsArray($field->value);
        $this->assertArrayHasKey('CLASS_ID', $field->value);
        $this->assertSame('CondGroup', $field->value['CLASS_ID']);
    }

    public function test_fillValue_with_invalid_base64_returns_empty_array(): void
    {
        Option::set('test.module', 'COND_KEY', '!!!');

        $field = Condition::make('COND_KEY');
        $field->setModuleId('test.module');
        $field->fillValue();

        $this->assertSame([], $field->value);
    }

    public function test_fillValue_with_serialized_false(): void
    {
        $data = base64_encode(serialize(false));
        Option::set('test.module', 'COND_KEY', $data);

        $field = Condition::make('COND_KEY');
        $field->setModuleId('test.module');
        $field->fillValue();

        $this->assertSame(false, $field->value);
    }

    public function test_fillValue_returns_empty_for_default(): void
    {
        $field = Condition::make('COND_KEY', 'Test Title');
        $field->setModuleId('test.module');
        $field->setDefault('test');
        $field->fillValue();

        $this->assertSame([], $field->value);
    }

    public function test_saveFromRequest_handles_request_data(): void
    {
        $field = Condition::make('COND_KEY');
        $field->setModuleId('test.module');

        $request = Application::getInstance()->getContext()->getRequest();
        $request->setParam('update', 'Y');
        $request->setParam('mid', 'test.module');
        $request->setParam('COND_KEY', 'some_data');

        $field->saveFromRequest();

        $expected = base64_encode(serialize(['CLASS_ID' => 'CondGroup', 'DATA' => []]));
        $this->assertSame($expected, Option::get('test.module', 'COND_KEY'));
    }

    public function test_saveFromRequest_returns_early_without_update(): void
    {
        $field = Condition::make('COND_KEY');
        $field->setModuleId('test.module');
        $field->saveFromRequest();

        $this->assertSame('', Option::get('test.module', 'COND_KEY'));
    }

    public function test_view_contains_escaped_title(): void
    {
        $field = Condition::make('COND_KEY', '<script>alert("XSS")</script>');
        $field->setModuleId('test.module');
        $field->fillValue();

        $html = $field->view();

        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_fluent_interface(): void
    {
        $field = Condition::make('COND_KEY');

        $this->assertInstanceOf(Condition::class, $field->setModuleId('test.module'));
    }
}
