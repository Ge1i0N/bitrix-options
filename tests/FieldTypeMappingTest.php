<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Tests;

use Gelion\BitrixOptionsMigration\Migrator;
use PHPUnit\Framework\TestCase;

final class FieldTypeMappingTest extends TestCase
{
    private Migrator $migrator;

    protected function setUp(): void
    {
        $this->migrator = new Migrator();
    }

    public function test_string_maps_to_text(): void
    {
        $this->assertSame('Text', $this->migrator->getFieldClass('STRING'));
    }

    public function test_number_maps_to_number(): void
    {
        $this->assertSame('Number', $this->migrator->getFieldClass('NUMBER'));
    }

    public function test_textarea_maps_to_textarea(): void
    {
        $this->assertSame('Textarea', $this->migrator->getFieldClass('TEXTAREA'));
    }

    public function test_checkbox_maps_to_checkbox(): void
    {
        $this->assertSame('Checkbox', $this->migrator->getFieldClass('CHECKBOX'));
    }

    public function test_colorpicker_maps_to_colorpicker(): void
    {
        $this->assertSame('ColorPicker', $this->migrator->getFieldClass('COLORPICKER'));
    }

    public function test_dropdown_maps_to_select(): void
    {
        $this->assertSame('Select', $this->migrator->getFieldClass('DROPDOWN'));
    }

    public function test_multiselect_maps_to_select(): void
    {
        $this->assertSame('Select', $this->migrator->getFieldClass('MULTISELECT'));
    }

    public function test_htmleditor_maps_to_htmleditor(): void
    {
        $this->assertSame('HtmlEditor', $this->migrator->getFieldClass('HTMLEDITOR'));
    }

    public function test_conditions_maps_to_condition(): void
    {
        $this->assertSame('Condition', $this->migrator->getFieldClass('CONDITIONS'));
    }

    public function test_alert_maps_to_alert(): void
    {
        $this->assertSame('Alert', $this->migrator->getFieldClass('ALERT'));
    }

    public function test_unknown_type_returns_null(): void
    {
        $this->assertNull($this->migrator->getFieldClass('CUSTOM_TYPE'));
    }

    public function test_all_ten_v1_types_are_mapped(): void
    {
        $v1Types = ['STRING', 'NUMBER', 'TEXTAREA', 'CHECKBOX', 'COLORPICKER', 'DROPDOWN', 'MULTISELECT', 'HTMLEDITOR', 'CONDITIONS', 'ALERT'];
        $mappedTypes = [];

        foreach ($v1Types as $type) {
            $class = $this->migrator->getFieldClass($type);
            $this->assertNotNull($class, "Type {$type} should have a mapping");
            $mappedTypes[$type] = $class;
        }

        $this->assertCount(10, $mappedTypes);
    }
}
