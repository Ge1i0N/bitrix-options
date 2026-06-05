<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Tests;

use Gelion\BitrixOptionsMigration\Migrator;
use PHPUnit\Framework\TestCase;

final class ParameterMappingTest extends TestCase
{
    private Migrator $migrator;

    protected function setUp(): void
    {
        $this->migrator = new Migrator();
    }

    public function test_default_maps_to_setDefault(): void
    {
        $this->assertSame('setDefault', $this->migrator->mapParameter('DEFAULT'));
    }

    public function test_notes_maps_to_setHint(): void
    {
        $this->assertSame('setHint', $this->migrator->mapParameter('NOTES'));
    }

    public function test_placeholder_maps_to_setPlaceholder(): void
    {
        $this->assertSame('setPlaceholder', $this->migrator->mapParameter('PLACEHOLDER'));
    }

    public function test_tag_maps_to_setTag(): void
    {
        $this->assertSame('setTag', $this->migrator->mapParameter('TAG'));
    }

    public function test_readonly_maps_to_readonly(): void
    {
        $this->assertSame('readonly', $this->migrator->mapParameter('READONLY'));
    }

    public function test_disabled_maps_to_disabled(): void
    {
        $this->assertSame('disabled', $this->migrator->mapParameter('DISABLED'));
    }

    public function test_autocomplete_maps_to_setAutocomplete(): void
    {
        $this->assertSame('setAutocomplete', $this->migrator->mapParameter('AUTOCOMPLETE'));
    }

    public function test_step_maps_to_step(): void
    {
        $this->assertSame('step', $this->migrator->mapParameter('STEP'));
    }

    public function test_min_maps_to_min(): void
    {
        $this->assertSame('min', $this->migrator->mapParameter('MIN'));
    }

    public function test_max_maps_to_max(): void
    {
        $this->assertSame('max', $this->migrator->mapParameter('MAX'));
    }

    public function test_cols_maps_to_cols(): void
    {
        $this->assertSame('cols', $this->migrator->mapParameter('COLS'));
    }

    public function test_rows_maps_to_rows(): void
    {
        $this->assertSame('rows', $this->migrator->mapParameter('ROWS'));
    }

    public function test_options_maps_to_options(): void
    {
        $this->assertSame('options', $this->migrator->mapParameter('OPTIONS'));
    }

    public function test_unknown_param_returns_null(): void
    {
        $this->assertNull($this->migrator->mapParameter('NONEXISTENT'));
    }

    public function test_step_is_valid_only_for_number(): void
    {
        $this->assertTrue($this->migrator->isValidForType('STEP', 'NUMBER'));
        $this->assertFalse($this->migrator->isValidForType('STEP', 'STRING'));
        $this->assertFalse($this->migrator->isValidForType('STEP', 'CHECKBOX'));
    }

    public function test_min_is_valid_only_for_number(): void
    {
        $this->assertTrue($this->migrator->isValidForType('MIN', 'NUMBER'));
        $this->assertFalse($this->migrator->isValidForType('MIN', 'TEXTAREA'));
    }

    public function test_max_is_valid_only_for_number(): void
    {
        $this->assertTrue($this->migrator->isValidForType('MAX', 'NUMBER'));
        $this->assertFalse($this->migrator->isValidForType('MAX', 'STRING'));
    }

    public function test_cols_is_valid_only_for_textarea(): void
    {
        $this->assertTrue($this->migrator->isValidForType('COLS', 'TEXTAREA'));
        $this->assertFalse($this->migrator->isValidForType('COLS', 'NUMBER'));
    }

    public function test_rows_is_valid_only_for_textarea(): void
    {
        $this->assertTrue($this->migrator->isValidForType('ROWS', 'TEXTAREA'));
        $this->assertFalse($this->migrator->isValidForType('ROWS', 'DROPDOWN'));
    }

    public function test_default_is_valid_for_all_types(): void
    {
        $this->assertTrue($this->migrator->isValidForType('DEFAULT', 'STRING'));
        $this->assertTrue($this->migrator->isValidForType('DEFAULT', 'NUMBER'));
        $this->assertTrue($this->migrator->isValidForType('DEFAULT', 'CHECKBOX'));
        $this->assertTrue($this->migrator->isValidForType('DEFAULT', 'DROPDOWN'));
    }

    public function test_unknown_param_is_invalid_for_any_type(): void
    {
        $this->assertFalse($this->migrator->isValidForType('NONEXISTENT', 'STRING'));
        $this->assertFalse($this->migrator->isValidForType('NONEXISTENT', 'NUMBER'));
    }

    public function test_is_type_specific_param_returns_true_for_step(): void
    {
        $this->assertTrue($this->migrator->isTypeSpecificParam('STEP'));
        $this->assertTrue($this->migrator->isTypeSpecificParam('MIN'));
        $this->assertTrue($this->migrator->isTypeSpecificParam('MAX'));
        $this->assertTrue($this->migrator->isTypeSpecificParam('COLS'));
        $this->assertTrue($this->migrator->isTypeSpecificParam('ROWS'));
        $this->assertTrue($this->migrator->isTypeSpecificParam('OPTIONS'));
    }

    public function test_is_type_specific_param_returns_false_for_standard_params(): void
    {
        $this->assertFalse($this->migrator->isTypeSpecificParam('DEFAULT'));
        $this->assertFalse($this->migrator->isTypeSpecificParam('NOTES'));
        $this->assertFalse($this->migrator->isTypeSpecificParam('PLACEHOLDER'));
        $this->assertFalse($this->migrator->isTypeSpecificParam('TAG'));
        $this->assertFalse($this->migrator->isTypeSpecificParam('READONLY'));
        $this->assertFalse($this->migrator->isTypeSpecificParam('DISABLED'));
        $this->assertFalse($this->migrator->isTypeSpecificParam('AUTOCOMPLETE'));
    }

    public function test_all_thirteen_params_are_mapped(): void
    {
        $params = ['DEFAULT', 'NOTES', 'PLACEHOLDER', 'TAG', 'READONLY', 'DISABLED', 'AUTOCOMPLETE', 'STEP', 'MIN', 'MAX', 'COLS', 'ROWS', 'OPTIONS'];
        $mappedParams = [];

        foreach ($params as $param) {
            $method = $this->migrator->mapParameter($param);
            $this->assertNotNull($method, "Parameter {$param} should have a mapping");
            $mappedParams[$param] = $method;
        }

        $this->assertCount(13, $mappedParams);
    }
}
