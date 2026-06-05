<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Tests;

use Gelion\BitrixOptionsMigration\MigrationResult;
use PHPUnit\Framework\TestCase;

final class MigrationResultTest extends TestCase
{
    public function test_constructor_sets_properties(): void
    {
        $result = new MigrationResult(
            code: '<?php echo "hello";',
            warnings: ['Deprecated function used'],
            imports: ['Bitrix\Main\Config\Option'],
            success: true,
        );

        $this->assertSame('<?php echo "hello";', $result->code);
        $this->assertSame(['Deprecated function used'], $result->warnings);
        $this->assertSame(['Bitrix\Main\Config\Option'], $result->imports);
        $this->assertTrue($result->success);
    }

    public function test_toArray_returns_correct_structure(): void
    {
        $result = new MigrationResult(
            code: 'test',
            warnings: ['warn'],
            imports: ['imp'],
            success: true,
        );

        $array = $result->toArray();

        $this->assertSame(['code', 'warnings', 'imports', 'success'], array_keys($array));
        $this->assertSame('test', $array['code']);
        $this->assertSame(['warn'], $array['warnings']);
        $this->assertSame(['imp'], $array['imports']);
        $this->assertTrue($array['success']);
    }

    public function test_hasWarnings_returns_false_when_empty(): void
    {
        $result = new MigrationResult();

        $this->assertFalse($result->hasWarnings());
    }

    public function test_hasWarnings_returns_true_when_warnings_exist(): void
    {
        $result = new MigrationResult(warnings: ['Some warning']);

        $this->assertTrue($result->hasWarnings());
    }

    public function test_default_values_are_empty(): void
    {
        $result = new MigrationResult();

        $this->assertSame('', $result->code);
        $this->assertSame([], $result->warnings);
        $this->assertSame([], $result->imports);
    }

    public function test_success_defaults_to_false(): void
    {
        $result = new MigrationResult();

        $this->assertFalse($result->success);
    }
}
