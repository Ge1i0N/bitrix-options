<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Tests\Security;

use Gelion\BitrixOptions\Fields\Condition;
use Gelion\BitrixOptions\Fields\HtmlEditor;
use Gelion\BitrixOptions\Fields\Select;
use PHPUnit\Framework\TestCase;

final class UnserializeTest extends TestCase
{
    protected function tearDown(): void
    {
        \Bitrix\Main\Application::reset();
        \Bitrix\Main\Config\Option::reset();
    }

    public function test_select_handles_serialized_false(): void
    {
        \Bitrix\Main\Config\Option::set('test.module', 'TEST_KEY', serialize(false));

        $select = Select::make('TEST_KEY');
        $select->setModuleId('test.module');
        $select->fillValue();

        $this->assertFalse($select->value);
        $this->assertIsBool($select->value);
    }

    public function test_select_handles_empty_string(): void
    {
        \Bitrix\Main\Config\Option::set('test.module', 'TEST_KEY', '');

        $select = Select::make('TEST_KEY');
        $select->setModuleId('test.module');
        $select->fillValue();

        $this->assertSame('', $select->value);
        $this->assertIsString($select->value);
    }

    public function test_htmleditor_handles_serialized_false(): void
    {
        \Bitrix\Main\Config\Option::set('test.module', 'TEST_KEY', serialize(false));

        $editor = HtmlEditor::make('TEST_KEY');
        $editor->setModuleId('test.module');
        $editor->fillValue();

        $this->assertSame(['type' => 'TEXT', 'value' => ''], $editor->value);
        $this->assertIsArray($editor->value);
    }

    public function test_htmleditor_handles_empty_string(): void
    {
        \Bitrix\Main\Config\Option::set('test.module', 'TEST_KEY', '');

        $editor = HtmlEditor::make('TEST_KEY');
        $editor->setModuleId('test.module');
        $editor->fillValue();

        $this->assertSame(['type' => 'TEXT', 'value' => ''], $editor->value);
    }

    public function test_condition_handles_invalid_base64(): void
    {
        \Bitrix\Main\Config\Option::set('test.module', 'TEST_KEY', '!!!invalid base64!!!');

        $condition = Condition::make('TEST_KEY');
        $condition->setModuleId('test.module');
        $condition->fillValue();

        $this->assertSame([], $condition->value);
    }

    public function test_condition_handles_serialized_false(): void
    {
        $stored = base64_encode(serialize(false));
        \Bitrix\Main\Config\Option::set('test.module', 'TEST_KEY', $stored);

        $condition = Condition::make('TEST_KEY');
        $condition->setModuleId('test.module');
        $condition->fillValue();

        $this->assertFalse($condition->value);
        $this->assertIsBool($condition->value);
    }

    public function test_select_handles_normal_serialized_array(): void
    {
        $data = ['a', 'b', 'c'];
        \Bitrix\Main\Config\Option::set('test.module', 'TEST_KEY', serialize($data));

        $select = Select::make('TEST_KEY');
        $select->setModuleId('test.module');
        $select->fillValue();

        $this->assertSame($data, $select->value);
    }
}
