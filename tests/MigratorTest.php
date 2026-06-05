<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Tests;

use Gelion\BitrixOptionsMigration\Migrator;
use Gelion\BitrixOptions\Tests\Migrator\TestCase;

final class MigratorTest extends TestCase
{
    public function test_convert_accepts_minimal_config(): void
    {
        $migrator = $this->createMigrator();
        $config = $this->createMinimalConfig();
        $result = $migrator->convert($config);

        $this->assertTrue($result->success);
        $this->assertInstanceOf(\Gelion\BitrixOptionsMigration\MigrationResult::class, $result);
    }

    public function test_convert_returns_code_string(): void
    {
        $migrator = $this->createMigrator();
        $config = $this->createMinimalConfig();
        $result = $migrator->convert($config);

        $this->assertNotEmpty($result->code);
    }

    public function test_convert_generates_php_opening_tag(): void
    {
        $migrator = $this->createMigrator();
        $config = $this->createMinimalConfig();
        $result = $migrator->convert($config);

        $this->assertStringStartsWith('<?php', $result->code);
    }

    public function test_convert_includes_form_make_call(): void
    {
        $migrator = $this->createMigrator();
        $config = $this->createMinimalConfig();
        $result = $migrator->convert($config);

        $this->assertGeneratedCodeContains('Form::make(', $result->code);
    }

    public function test_convert_includes_tab_make_call(): void
    {
        $migrator = $this->createMigrator();
        $config = $this->createMinimalConfig();
        $result = $migrator->convert($config);

        $this->assertGeneratedCodeContains('Tab::make(', $result->code);
    }

    public function test_convert_missing_module_id_returns_failure(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'tabs' => [],
        ];
        $result = $migrator->convert($config);

        $this->assertFalse($result->success);
        $this->assertTrue($result->hasWarnings());
        $this->assertSame('missing_module_id', $result->warnings[0]['type']);
    }

    public function test_convert_missing_tabs_returns_failure(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'test.module',
        ];
        $result = $migrator->convert($config);

        $this->assertFalse($result->success);
        $this->assertTrue($result->hasWarnings());
        $this->assertSame('missing_tabs', $result->warnings[0]['type']);
    }

    public function test_convert_unknown_field_type_adds_warning(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'test.module',
            'tabs' => [
                [
                    'DIV' => 'tab1',
                    'TAB' => 'Tab 1',
                    'GROUPS' => [
                        [
                            'TITLE' => 'Group 1',
                            'OPTIONS' => [
                                'CUSTOM_FIELD' => ['TYPE' => 'CUSTOM_FIELD', 'TITLE' => 'Custom'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = $migrator->convert($config);

        $this->assertTrue($result->success);
        $this->assertTrue($result->hasWarnings());
        $this->assertSame('unknown_field_type', $result->warnings[0]['type']);
        $this->assertGeneratedCodeContains("CUSTOM_FIELD: Неизвестный тип 'CUSTOM_FIELD'", $result->code);
    }

    public function test_convert_alert_field_omits_key(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'test.module',
            'tabs' => [
                [
                    'DIV' => 'tab1',
                    'TAB' => 'Tab 1',
                    'GROUPS' => [
                        [
                            'OPTIONS' => [
                                'MY_ALERT' => ['TYPE' => 'ALERT', 'TITLE' => 'Info message'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = $migrator->convert($config);

        $this->assertStringContainsString('Alert::make(', $result->code);
        $this->assertStringNotContainsString("Alert::make('MY_ALERT'", $result->code);
        $this->assertStringContainsString("Alert::make('Info message'", $result->code);
    }

    public function test_convert_generates_use_statements(): void
    {
        $migrator = $this->createMigrator();
        $config = $this->createMinimalConfig();
        $result = $migrator->convert($config);

        $this->assertGeneratedCodeContains('use Gelion\BitrixOptions\Form;', $result->code);
        $this->assertGeneratedCodeContains('use Gelion\BitrixOptions\Tab;', $result->code);
        $this->assertGeneratedCodeContains('use Gelion\BitrixOptions\Fields\Text;', $result->code);
    }

    public function test_convert_group_with_title_adds_heading(): void
    {
        $migrator = $this->createMigrator();
        $config = $this->createMinimalConfig();
        $result = $migrator->convert($config);

        $this->assertGeneratedCodeContains("Heading::make('Group 1')", $result->code);
        $this->assertGeneratedCodeContains('use Gelion\BitrixOptions\Fields\Heading;', $result->code);
    }

    public function test_convert_group_without_title_skips_heading(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'test.module',
            'tabs' => [
                [
                    'DIV' => 'tab1',
                    'TAB' => 'Tab 1',
                    'GROUPS' => [
                        [
                            'OPTIONS' => [
                                'MY_OPTION' => ['TYPE' => 'STRING', 'TITLE' => 'My Option'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = $migrator->convert($config);

        $this->assertGeneratedCodeNotContains('Heading::make(', $result->code);
        $this->assertGeneratedCodeContains('Text::make(', $result->code);
    }

    public function test_generated_code_passes_syntax_check(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'test.module',
            'tabs' => [
                [
                    'DIV' => 'main',
                    'TAB' => 'Main Settings',
                    'GROUPS' => [
                        [
                            'TITLE' => 'General',
                            'OPTIONS' => [
                                'SITE_NAME' => ['TYPE' => 'STRING', 'TITLE' => 'Site Name'],
                                'ENABLE_CACHE' => ['TYPE' => 'CHECKBOX', 'TITLE' => 'Enable Cache'],
                                'MAX_ITEMS' => ['TYPE' => 'NUMBER', 'TITLE' => 'Max Items'],
                                'DESCRIPTION' => ['TYPE' => 'TEXTAREA', 'TITLE' => 'Description'],
                            ],
                        ],
                        [
                            'OPTIONS' => [
                                'ALERT_MSG' => ['TYPE' => 'ALERT', 'TITLE' => 'Configuration notice'],
                                'COLOR' => ['TYPE' => 'COLORPICKER', 'TITLE' => 'Theme Color'],
                            ],
                        ],
                    ],
                ],
                [
                    'DIV' => 'advanced',
                    'TAB' => 'Advanced',
                    'OPTIONS' => [
                        'MODE' => ['TYPE' => 'DROPDOWN', 'TITLE' => 'Mode'],
                    ],
                ],
            ],
        ];
        $result = $migrator->convert($config);

        $tempFile = tempnam(sys_get_temp_dir(), 'migrator_test_');
        file_put_contents($tempFile, $result->code);

        $output = [];
        $exitCode = 0;
        exec('php -l ' . escapeshellarg($tempFile) . ' 2>&1', $output, $exitCode);

        unlink($tempFile);

        $this->assertSame(0, $exitCode, 'Generated PHP code has syntax errors: ' . implode("\n", $output));
    }

    public function test_fields_are_sorted_by_sort_value(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'test.module',
            'tabs' => [
                [
                    'DIV' => 'tab1',
                    'TAB' => 'Tab 1',
                    'GROUPS' => [
                        [
                            'OPTIONS' => [
                                'FIELD_A' => ['TYPE' => 'STRING', 'TITLE' => 'Field A', 'SORT' => 300],
                                'FIELD_B' => ['TYPE' => 'STRING', 'TITLE' => 'Field B', 'SORT' => 100],
                                'FIELD_C' => ['TYPE' => 'STRING', 'TITLE' => 'Field C', 'SORT' => 200],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = $migrator->convert($config);

        $code = $result->code;
        $posB = strpos($code, "Text::make('FIELD_B'");
        $posC = strpos($code, "Text::make('FIELD_C'");
        $posA = strpos($code, "Text::make('FIELD_A'");

        $this->assertNotFalse($posB, 'FIELD_B should be present');
        $this->assertNotFalse($posC, 'FIELD_C should be present');
        $this->assertNotFalse($posA, 'FIELD_A should be present');

        $this->assertTrue($posB < $posC, 'FIELD_B (SORT=100) should come before FIELD_C (SORT=200)');
        $this->assertTrue($posC < $posA, 'FIELD_C (SORT=200) should come before FIELD_A (SORT=300)');
    }

    public function test_fields_without_sort_get_default_500(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'test.module',
            'tabs' => [
                [
                    'DIV' => 'tab1',
                    'TAB' => 'Tab 1',
                    'GROUPS' => [
                        [
                            'OPTIONS' => [
                                'FIELD_A' => ['TYPE' => 'STRING', 'TITLE' => 'Field A', 'SORT' => 100],
                                'FIELD_B' => ['TYPE' => 'STRING', 'TITLE' => 'Field B'],
                                'FIELD_C' => ['TYPE' => 'STRING', 'TITLE' => 'Field C', 'SORT' => 600],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = $migrator->convert($config);

        $code = $result->code;
        $posA = strpos($code, "Text::make('FIELD_A'");
        $posB = strpos($code, "Text::make('FIELD_B'");
        $posC = strpos($code, "Text::make('FIELD_C'");

        $this->assertNotFalse($posA, 'FIELD_A should be present');
        $this->assertNotFalse($posB, 'FIELD_B should be present');
        $this->assertNotFalse($posC, 'FIELD_C should be present');

        $this->assertTrue($posA < $posB, 'FIELD_A (SORT=100) should come before FIELD_B (default 500)');
        $this->assertTrue($posB < $posC, 'FIELD_B (default 500) should come before FIELD_C (SORT=600)');
    }

    public function test_identical_sort_values_preserve_order(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'test.module',
            'tabs' => [
                [
                    'DIV' => 'tab1',
                    'TAB' => 'Tab 1',
                    'GROUPS' => [
                        [
                            'OPTIONS' => [
                                'FIELD_A' => ['TYPE' => 'STRING', 'TITLE' => 'First', 'SORT' => 100],
                                'FIELD_B' => ['TYPE' => 'STRING', 'TITLE' => 'Second', 'SORT' => 100],
                                'FIELD_C' => ['TYPE' => 'STRING', 'TITLE' => 'Third', 'SORT' => 100],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = $migrator->convert($config);

        $code = $result->code;
        $posA = strpos($code, "Text::make('FIELD_A'");
        $posB = strpos($code, "Text::make('FIELD_B'");
        $posC = strpos($code, "Text::make('FIELD_C'");

        $this->assertNotFalse($posA);
        $this->assertNotFalse($posB);
        $this->assertNotFalse($posC);

        $this->assertTrue($posA < $posB, 'FIELD_A (first in array) should come before FIELD_B (same SORT)');
        $this->assertTrue($posB < $posC, 'FIELD_B (middle) should come before FIELD_C (last, same SORT)');
    }

    public function test_empty_group_returns_empty(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'test.module',
            'tabs' => [
                [
                    'DIV' => 'tab1',
                    'TAB' => 'Tab 1',
                    'GROUPS' => [
                        [
                            'TITLE' => 'Empty Group',
                            'OPTIONS' => [],
                        ],
                        [
                            'TITLE' => 'Non-Empty Group',
                            'OPTIONS' => [
                                'MY_OPTION' => ['TYPE' => 'STRING', 'TITLE' => 'My Option'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = $migrator->convert($config);

        $code = $result->code;

        // Only one Heading for the non-empty group should appear
        $this->assertGeneratedCodeContains("Heading::make('Non-Empty Group'", $code);
        $this->assertGeneratedCodeNotContains("Heading::make('Empty Group'", $code);
        $this->assertGeneratedCodeContains("Text::make('MY_OPTION'", $code);
    }

    public function test_tab_without_groups_and_direct_options(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'test.module',
            'tabs' => [
                [
                    'DIV' => 'tab1',
                    'TAB' => 'Tab 1',
                    'OPTIONS' => [
                        'FIELD_A' => ['TYPE' => 'STRING', 'TITLE' => 'Field A'],
                        'FIELD_B' => ['TYPE' => 'NUMBER', 'TITLE' => 'Field B'],
                    ],
                ],
            ],
        ];
        $result = $migrator->convert($config);

        $this->assertTrue($result->success);
        $this->assertGeneratedCodeContains("Text::make('FIELD_A'", $result->code);
        $this->assertGeneratedCodeContains("Number::make('FIELD_B'", $result->code);
    }

    public function test_convert_field_dispatches_by_type(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'test.module',
            'tabs' => [
                [
                    'DIV' => 'tab1',
                    'TAB' => 'Tab 1',
                    'GROUPS' => [
                        [
                            'OPTIONS' => [
                                'F_STRING' => ['TYPE' => 'STRING', 'TITLE' => 'String'],
                                'F_NUMBER' => ['TYPE' => 'NUMBER', 'TITLE' => 'Number'],
                                'F_TEXTAREA' => ['TYPE' => 'TEXTAREA', 'TITLE' => 'Textarea'],
                                'F_CHECKBOX' => ['TYPE' => 'CHECKBOX', 'TITLE' => 'Checkbox'],
                                'F_COLORPICKER' => ['TYPE' => 'COLORPICKER', 'TITLE' => 'Color'],
                                'F_DROPDOWN' => ['TYPE' => 'DROPDOWN', 'TITLE' => 'Dropdown'],
                                'F_MULTISELECT' => ['TYPE' => 'MULTISELECT', 'TITLE' => 'Multi'],
                                'F_HTMLEDITOR' => ['TYPE' => 'HTMLEDITOR', 'TITLE' => 'Html'],
                                'F_CONDITIONS' => ['TYPE' => 'CONDITIONS', 'TITLE' => 'Condition'],
                                'F_ALERT' => ['TYPE' => 'ALERT', 'TITLE' => 'Alert message'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = $migrator->convert($config);

        $this->assertTrue($result->success);

        // All 10 types should produce expected class names
        $this->assertGeneratedCodeContains("Text::make('F_STRING'", $result->code);
        $this->assertGeneratedCodeContains("Number::make('F_NUMBER'", $result->code);
        $this->assertGeneratedCodeContains("Textarea::make('F_TEXTAREA'", $result->code);
        $this->assertGeneratedCodeContains("Checkbox::make('F_CHECKBOX'", $result->code);
        $this->assertGeneratedCodeContains("ColorPicker::make('F_COLORPICKER'", $result->code);
        $this->assertGeneratedCodeContains("Select::make('F_DROPDOWN'", $result->code);
        $this->assertGeneratedCodeContains("Select::make('F_MULTISELECT'", $result->code);
        $this->assertGeneratedCodeContains("HtmlEditor::make('F_HTMLEDITOR'", $result->code);
        $this->assertGeneratedCodeContains("Condition::make('F_CONDITIONS'", $result->code);
        // ALERT: no key, title only
        $this->assertGeneratedCodeContains("Alert::make('Alert message'", $result->code);
        $this->assertGeneratedCodeNotContains("Alert::make('F_ALERT'", $result->code);
    }

    // ----- Per-type converter tests (Tasks 10-17) -----

    /**
     * Helper: invoke private convertField method with proper reference handling.
     */
    private function invokeConvertField(\Gelion\BitrixOptionsMigration\Migrator $migrator, string $key, array $field, array &$usedClasses): string
    {
        $closure = \Closure::bind(
            function (string $k, array $f, array &$classes): string {
                return $this->convertField($k, $f, $classes);
            },
            $migrator,
            \Gelion\BitrixOptionsMigration\Migrator::class
        );

        return $closure($key, $field, $usedClasses);
    }

    public function test_string_field_converts_to_text(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'SITE_NAME', ['TYPE' => 'STRING', 'TITLE' => 'Site Name'], $usedClasses);

        $this->assertSame("Text::make('SITE_NAME', 'Site Name'),", $result);
        $this->assertContains('Text', $usedClasses);
    }

    public function test_number_field_converts_to_number(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'MAX_ITEMS', ['TYPE' => 'NUMBER', 'TITLE' => 'Max Items'], $usedClasses);

        $this->assertSame("Number::make('MAX_ITEMS', 'Max Items'),", $result);
        $this->assertContains('Number', $usedClasses);
    }

    public function test_checkbox_field_converts_to_checkbox(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'ENABLE_CACHE', ['TYPE' => 'CHECKBOX', 'TITLE' => 'Enable Cache'], $usedClasses);

        $this->assertSame("Checkbox::make('ENABLE_CACHE', 'Enable Cache'),", $result);
        $this->assertContains('Checkbox', $usedClasses);
    }

    public function test_textarea_field_converts_to_textarea(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'DESCRIPTION', ['TYPE' => 'TEXTAREA', 'TITLE' => 'Description'], $usedClasses);

        $this->assertSame("Textarea::make('DESCRIPTION', 'Description'),", $result);
        $this->assertContains('Textarea', $usedClasses);
    }

    public function test_dropdown_field_converts_to_select(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'MODE', ['TYPE' => 'DROPDOWN', 'TITLE' => 'Mode'], $usedClasses);

        $this->assertSame("Select::make('MODE', 'Mode'),", $result);
        $this->assertContains('Select', $usedClasses);
    }

    public function test_multiselect_field_adds_multiple(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'COLORS', ['TYPE' => 'MULTISELECT', 'TITLE' => 'Colors'], $usedClasses);

        $this->assertSame("Select::make('COLORS', 'Colors')->multiple(),", $result);
        $this->assertContains('Select', $usedClasses);
    }

    public function test_colorpicker_field_converts_to_colorpicker(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'THEME_COLOR', ['TYPE' => 'COLORPICKER', 'TITLE' => 'Theme Color'], $usedClasses);

        $this->assertSame("ColorPicker::make('THEME_COLOR', 'Theme Color'),", $result);
        $this->assertContains('ColorPicker', $usedClasses);
    }

    public function test_htmleditor_field_converts_to_htmleditor(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'CONTENT', ['TYPE' => 'HTMLEDITOR', 'TITLE' => 'Content'], $usedClasses);

        $this->assertSame("HtmlEditor::make('CONTENT', 'Content'),", $result);
        $this->assertContains('HtmlEditor', $usedClasses);
    }

    public function test_conditions_field_converts_to_condition(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'RULE', ['TYPE' => 'CONDITIONS', 'TITLE' => 'Rule'], $usedClasses);

        $this->assertSame("Condition::make('RULE', 'Rule'),", $result);
        $this->assertContains('Condition', $usedClasses);
    }

    public function test_alert_field_generates_no_key(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'MY_ALERT', ['TYPE' => 'ALERT', 'TITLE' => 'Notice'], $usedClasses);

        $this->assertSame("Alert::make('Notice'),", $result);
        $this->assertContains('Alert', $usedClasses);
    }

    public function test_all_ten_field_types_work(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'test.module',
            'tabs' => [
                [
                    'DIV' => 'tab1',
                    'TAB' => 'Tab 1',
                    'OPTIONS' => [
                        'F_STRING' => ['TYPE' => 'STRING', 'TITLE' => 'String'],
                        'F_NUMBER' => ['TYPE' => 'NUMBER', 'TITLE' => 'Number'],
                        'F_CHECKBOX' => ['TYPE' => 'CHECKBOX', 'TITLE' => 'Checkbox'],
                        'F_TEXTAREA' => ['TYPE' => 'TEXTAREA', 'TITLE' => 'Textarea'],
                        'F_DROPDOWN' => ['TYPE' => 'DROPDOWN', 'TITLE' => 'Dropdown'],
                        'F_MULTISELECT' => ['TYPE' => 'MULTISELECT', 'TITLE' => 'Multi'],
                        'F_COLORPICKER' => ['TYPE' => 'COLORPICKER', 'TITLE' => 'Color'],
                        'F_HTMLEDITOR' => ['TYPE' => 'HTMLEDITOR', 'TITLE' => 'Html'],
                        'F_CONDITIONS' => ['TYPE' => 'CONDITIONS', 'TITLE' => 'Condition'],
                        'F_ALERT' => ['TYPE' => 'ALERT', 'TITLE' => 'Alert message'],
                    ],
                ],
            ],
        ];
        $result = $migrator->convert($config);

        $this->assertTrue($result->success);

        $code = $result->code;

        // Verify per-type output with exact string matches
        $this->assertStringContainsString("Text::make('F_STRING', 'String'),", $code);
        $this->assertStringContainsString("Number::make('F_NUMBER', 'Number'),", $code);
        $this->assertStringContainsString("Checkbox::make('F_CHECKBOX', 'Checkbox'),", $code);
        $this->assertStringContainsString("Textarea::make('F_TEXTAREA', 'Textarea'),", $code);
        $this->assertStringContainsString("Select::make('F_DROPDOWN', 'Dropdown'),", $code);
        $this->assertStringContainsString("Select::make('F_MULTISELECT', 'Multi')->multiple(),", $code);
        $this->assertStringContainsString("ColorPicker::make('F_COLORPICKER', 'Color'),", $code);
        $this->assertStringContainsString("HtmlEditor::make('F_HTMLEDITOR', 'Html'),", $code);
        $this->assertStringContainsString("Condition::make('F_CONDITIONS', 'Condition'),", $code);
        $this->assertStringContainsString("Alert::make('Alert message'),", $code);
        $this->assertStringNotContainsString("Alert::make('F_ALERT'", $code);

        // Verify PHP syntax passes
        $tempFile = tempnam(sys_get_temp_dir(), 'migrator_test_all_');
        file_put_contents($tempFile, $code);
        $output = [];
        $exitCode = 0;
        exec('php -l ' . escapeshellarg($tempFile) . ' 2>&1', $output, $exitCode);
        unlink($tempFile);

        $this->assertSame(0, $exitCode, 'Generated PHP code has syntax errors: ' . implode("\n", $output));
    }

    // =========================================================================
    // Task 18-21: Parameter Mapping Tests
    // =========================================================================

    public function test_default_parameter_adds_setdefault(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'SITE_NAME', [
            'TYPE' => 'STRING',
            'TITLE' => 'Site Name',
            'DEFAULT' => 'my_value',
        ], $usedClasses);

        $this->assertStringContainsString("->setDefault('my_value')", $result);
        $this->assertStringStartsWith("Text::make('SITE_NAME', 'Site Name')", $result);
    }

    public function test_notes_parameter_adds_sethint(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'OPTION', [
            'TYPE' => 'STRING',
            'TITLE' => 'Option',
            'NOTES' => 'help text',
        ], $usedClasses);

        $this->assertStringContainsString("->setHint('help text')", $result);
    }

    public function test_placeholder_parameter_adds_setplaceholder(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'EMAIL', [
            'TYPE' => 'STRING',
            'TITLE' => 'Email',
            'PLACEHOLDER' => 'enter text',
        ], $usedClasses);

        $this->assertStringContainsString("->setPlaceholder('enter text')", $result);
    }

    public function test_tag_and_tagcolor_generate_settag(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'OPTION', [
            'TYPE' => 'STRING',
            'TITLE' => 'Option',
            'TAG' => 'beta',
            'TAGCOLOR' => 'success',
        ], $usedClasses);

        $this->assertStringContainsString("->setTag('beta', \\Gelion\\BitrixOptions\\Enums\\TagColor::SUCCESS)", $result);
        $this->assertContains('TagColor', $usedClasses);
    }

    public function test_tagcolor_default_when_missing(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'OPTION', [
            'TYPE' => 'STRING',
            'TITLE' => 'Option',
            'TAG' => 'alpha',
        ], $usedClasses);

        $this->assertStringContainsString("\\TagColor::DEFAULT)", $result);
    }

    public function test_readonly_true_adds_readonly(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'OPTION', [
            'TYPE' => 'STRING',
            'TITLE' => 'Option',
            'READONLY' => true,
        ], $usedClasses);

        $this->assertStringContainsString('->readonly(true)', $result);
    }

    public function test_readonly_false_skips_readonly(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'OPTION', [
            'TYPE' => 'STRING',
            'TITLE' => 'Option',
            'READONLY' => false,
        ], $usedClasses);

        $this->assertStringNotContainsString('->readonly', $result);
    }

    public function test_disabled_true_adds_disabled(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'OPTION', [
            'TYPE' => 'STRING',
            'TITLE' => 'Option',
            'DISABLED' => true,
        ], $usedClasses);

        $this->assertStringContainsString('->disabled(true)', $result);
    }

    public function test_disabled_false_skips_disabled(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'OPTION', [
            'TYPE' => 'STRING',
            'TITLE' => 'Option',
            'DISABLED' => false,
        ], $usedClasses);

        $this->assertStringNotContainsString('->disabled', $result);
    }

    public function test_autocomplete_parameter(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'OPTION', [
            'TYPE' => 'STRING',
            'TITLE' => 'Option',
            'AUTOCOMPLETE' => 'username',
        ], $usedClasses);

        $this->assertStringContainsString("->setAutocomplete('username')", $result);
    }

    public function test_number_min_max_step(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'COUNT', [
            'TYPE' => 'NUMBER',
            'TITLE' => 'Count',
            'STEP' => 2,
            'MIN' => 0,
            'MAX' => 100,
        ], $usedClasses);

        $this->assertStringContainsString('->step(2)', $result);
        $this->assertStringContainsString('->min(0)', $result);
        $this->assertStringContainsString('->max(100)', $result);
    }

    public function test_textarea_cols_rows(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'DESC', [
            'TYPE' => 'TEXTAREA',
            'TITLE' => 'Description',
            'COLS' => 10,
            'ROWS' => 5,
        ], $usedClasses);

        $this->assertStringContainsString('->cols(10)', $result);
        $this->assertStringContainsString('->rows(5)', $result);
    }

    public function test_dropdown_size(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'MODE', [
            'TYPE' => 'DROPDOWN',
            'TITLE' => 'Mode',
            'SIZE' => 5,
        ], $usedClasses);

        $this->assertStringContainsString('->size(5)', $result);
    }

    public function test_options_format_transformation(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'COLOR', [
            'TYPE' => 'DROPDOWN',
            'TITLE' => 'Color',
            'OPTIONS' => [
                ['TITLE' => 'Red', 'VALUE' => 'red'],
                ['TITLE' => 'Green', 'VALUE' => 'green'],
                ['TITLE' => 'Blue', 'VALUE' => 'blue'],
            ],
        ], $usedClasses);

        $this->assertStringContainsString("->options(['red' => 'Red', 'green' => 'Green', 'blue' => 'Blue'])", $result);
    }

    public function test_empty_options_skipped(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'MODE', [
            'TYPE' => 'DROPDOWN',
            'TITLE' => 'Mode',
        ], $usedClasses);

        $this->assertStringNotContainsString('->options(', $result);
        $this->assertSame("Select::make('MODE', 'Mode'),", $result);
    }

    public function test_type_specific_param_on_wrong_type_warns(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'OPTION', [
            'TYPE' => 'STRING',
            'TITLE' => 'Option',
            'STEP' => 5,
        ], $usedClasses);

        // STEP on STRING should trigger parameter_type_mismatch warning
        // (STEP is a NUMBER-specific param used on STRING)
        $this->assertHasWarning($migrator, 'parameter_type_mismatch');
        $this->assertStringContainsString("Text::make('OPTION', 'Option')", $result);
    }

    public function test_number_with_cols_warns_type_mismatch(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $this->invokeConvertField($migrator, 'COUNT', [
            'TYPE' => 'NUMBER',
            'TITLE' => 'Count',
            'COLS' => 10,
        ], $usedClasses);

        $this->assertHasWarning($migrator, 'parameter_type_mismatch');
    }

    public function test_textarea_with_step_warns_type_mismatch(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $this->invokeConvertField($migrator, 'DESC', [
            'TYPE' => 'TEXTAREA',
            'TITLE' => 'Desc',
            'STEP' => 2,
        ], $usedClasses);

        $this->assertHasWarning($migrator, 'parameter_type_mismatch');
    }

    public function test_unknown_parameter_warns(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $this->invokeConvertField($migrator, 'OPTION', [
            'TYPE' => 'STRING',
            'TITLE' => 'Option',
            'UNKNOWN_PARAM' => 'some_value',
        ], $usedClasses);

        $this->assertHasWarning($migrator, 'unknown_parameter');
    }

    public function test_boolean_false_parameters_are_skipped(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'OPTION', [
            'TYPE' => 'STRING',
            'TITLE' => 'Option',
            'READONLY' => false,
            'DISABLED' => false,
        ], $usedClasses);

        // Should produce simple output without any methods
        $this->assertSame("Text::make('OPTION', 'Option'),", $result);
    }

    public function test_tagcolor_enum_import_added(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'test.module',
            'tabs' => [
                [
                    'DIV' => 'tab1',
                    'TAB' => 'Tab 1',
                    'OPTIONS' => [
                        'FIELD_WITH_TAG' => [
                            'TYPE' => 'STRING',
                            'TITLE' => 'With Tag',
                            'TAG' => 'new',
                            'TAGCOLOR' => 'primary',
                        ],
                    ],
                ],
            ],
        ];
        $result = $migrator->convert($config);

        $this->assertTrue($result->success);
        $this->assertGeneratedCodeContains('use Gelion\BitrixOptions\Enums\TagColor;', $result->code);
        $this->assertGeneratedCodeContains('TagColor::PRIMARY', $result->code);
    }

    public function test_full_config_with_all_parameters_passes_syntax(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'test.module',
            'tabs' => [
                [
                    'DIV' => 'main',
                    'TAB' => 'Main',
                    'OPTIONS' => [
                        'SITE_NAME' => [
                            'TYPE' => 'STRING',
                            'TITLE' => 'Site Name',
                            'DEFAULT' => 'My Site',
                            'NOTES' => 'This is the site name',
                            'PLACEHOLDER' => 'Enter name',
                            'READONLY' => true,
                            'DISABLED' => false,
                            'AUTOCOMPLETE' => 'off',
                            'TAG' => 'beta',
                            'TAGCOLOR' => 'warning',
                        ],
                        'MAX_ITEMS' => [
                            'TYPE' => 'NUMBER',
                            'TITLE' => 'Max Items',
                            'DEFAULT' => '10',
                            'STEP' => 1,
                            'MIN' => 0,
                            'MAX' => 100,
                        ],
                        'DESCRIPTION' => [
                            'TYPE' => 'TEXTAREA',
                            'TITLE' => 'Description',
                            'COLS' => 80,
                            'ROWS' => 10,
                        ],
                        'MODE' => [
                            'TYPE' => 'DROPDOWN',
                            'TITLE' => 'Mode',
                            'SIZE' => 3,
                            'OPTIONS' => [
                                ['TITLE' => 'Auto', 'VALUE' => 'auto'],
                                ['TITLE' => 'Manual', 'VALUE' => 'manual'],
                            ],
                        ],
                        'COLORS' => [
                            'TYPE' => 'MULTISELECT',
                            'TITLE' => 'Colors',
                            'SIZE' => 5,
                            'OPTIONS' => [
                                ['TITLE' => 'Red', 'VALUE' => 'red'],
                                ['TITLE' => 'Blue', 'VALUE' => 'blue'],
                            ],
                        ],
                        'ALERT_MSG' => [
                            'TYPE' => 'ALERT',
                            'TITLE' => 'Notice: all settings are case-sensitive',
                        ],
                    ],
                ],
            ],
        ];
        $result = $migrator->convert($config);

        $this->assertTrue($result->success);

        $code = $result->code;

        // Verify standard params
        $this->assertStringContainsString("->setDefault('My Site')", $code);
        $this->assertStringContainsString("->setHint('This is the site name')", $code);
        $this->assertStringContainsString("->setPlaceholder('Enter name')", $code);
        $this->assertStringContainsString('->readonly(true)', $code);
        $this->assertStringContainsString("->setAutocomplete('off')", $code);
        $this->assertStringContainsString("->setTag('beta', \\Gelion\\BitrixOptions\\Enums\\TagColor::WARNING)", $code);

        // Verify type-specific params
        $this->assertStringContainsString('->step(1)', $code);
        $this->assertStringContainsString('->min(0)', $code);
        $this->assertStringContainsString('->max(100)', $code);
        $this->assertStringContainsString('->cols(80)', $code);
        $this->assertStringContainsString('->rows(10)', $code);
        $this->assertStringContainsString('->size(5)', $code);

        // Verify options transformation
        $this->assertStringContainsString("->options(['auto' => 'Auto', 'manual' => 'Manual'])", $code);
        $this->assertStringContainsString("->options(['red' => 'Red', 'blue' => 'Blue'])", $code);

        // Verify MULTISELECT still has ->multiple()
        $this->assertStringContainsString("->multiple()", $code);

        // Verify TagColor import
        $this->assertGeneratedCodeContains('use Gelion\BitrixOptions\Enums\TagColor;', $code);

        // Verify PHP syntax passes
        $tempFile = tempnam(sys_get_temp_dir(), 'migrator_params_');
        file_put_contents($tempFile, $code);
        $output = [];
        $exitCode = 0;
        exec('php -l ' . escapeshellarg($tempFile) . ' 2>&1', $output, $exitCode);
        unlink($tempFile);

        $this->assertSame(0, $exitCode, 'Generated PHP code has syntax errors: ' . implode("\n", $output));
    }

    public function test_option_value_fallback_to_title_when_missing_title(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'MODE', [
            'TYPE' => 'DROPDOWN',
            'TITLE' => 'Mode',
            'OPTIONS' => [
                ['VALUE' => 'opt1'],
                ['VALUE' => 'opt2', 'TITLE' => 'Option 2'],
            ],
        ], $usedClasses);

        $this->assertStringContainsString("'opt1' => 'opt1'", $result);
        $this->assertStringContainsString("'opt2' => 'Option 2'", $result);
    }

    public function test_invalid_options_format_warns(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $this->invokeConvertField($migrator, 'MODE', [
            'TYPE' => 'DROPDOWN',
            'TITLE' => 'Mode',
            'OPTIONS' => [
                ['TITLE' => 'No Value'],
                ['VALUE' => 'valid', 'TITLE' => 'Valid'],
            ],
        ], $usedClasses);

        $this->assertHasWarning($migrator, 'invalid_options_format');
    }

    public function test_empty_string_default_should_not_generate(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'OPTION', [
            'TYPE' => 'STRING',
            'TITLE' => 'Option',
            'DEFAULT' => '',
        ], $usedClasses);

        $this->assertStringNotContainsString('setDefault', $result);
    }

    public function test_multiple_parameters_chained_together(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'OPTION', [
            'TYPE' => 'STRING',
            'TITLE' => 'Option',
            'DEFAULT' => 'val',
            'NOTES' => 'hint',
            'PLACEHOLDER' => 'ph',
        ], $usedClasses);

        $this->assertStringContainsString("->setDefault('val')", $result);
        $this->assertStringContainsString("->setHint('hint')", $result);
        $this->assertStringContainsString("->setPlaceholder('ph')", $result);
        // Verify the order: setDefault before setHint before setPlaceholder
        $this->assertTrue(
            strpos($result, 'setDefault') < strpos($result, 'setHint'),
            'setDefault should come before setHint'
        );
        $this->assertTrue(
            strpos($result, 'setHint') < strpos($result, 'setPlaceholder'),
            'setHint should come before setPlaceholder'
        );
    }

    // =========================================================================
    // Tasks 22-24: Code Generation Improvements
    // =========================================================================

    public function test_imports_are_grouped_and_sorted(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'test.module',
            'tabs' => [
                [
                    'DIV' => 'main',
                    'TAB' => 'Main',
                    'GROUPS' => [
                        [
                            'TITLE' => 'General',
                            'OPTIONS' => [
                                'SITE_NAME' => ['TYPE' => 'STRING', 'TITLE' => 'Site Name', 'TAG' => 'new', 'TAGCOLOR' => 'primary'],
                                'MAX_ITEMS' => ['TYPE' => 'NUMBER', 'TITLE' => 'Max Items'],
                                'ENABLE' => ['TYPE' => 'CHECKBOX', 'TITLE' => 'Enable'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = $migrator->convert($config);

        $imports = $result->imports;
        $expectedOrder = [
            'use Gelion\BitrixOptions\Form;',
            'use Gelion\BitrixOptions\Tab;',
            'use Gelion\BitrixOptions\Fields\Checkbox;',
            'use Gelion\BitrixOptions\Fields\Heading;',
            'use Gelion\BitrixOptions\Fields\Number;',
            'use Gelion\BitrixOptions\Fields\Text;',
            'use Gelion\BitrixOptions\Enums\TagColor;',
        ];

        $this->assertSame($expectedOrder, $imports);
    }

    public function test_tagcolor_import_uses_enums_namespace(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'test.module',
            'tabs' => [
                [
                    'DIV' => 'tab1',
                    'TAB' => 'Tab 1',
                    'OPTIONS' => [
                        'FIELD' => [
                            'TYPE' => 'STRING',
                            'TITLE' => 'With Tag',
                            'TAG' => 'new',
                            'TAGCOLOR' => 'primary',
                        ],
                    ],
                ],
            ],
        ];
        $result = $migrator->convert($config);

        $this->assertGeneratedCodeContains('use Gelion\BitrixOptions\Enums\TagColor;', $result->code);
        $this->assertStringContainsString('TagColor::PRIMARY', $result->code);
    }

    public function test_only_used_classes_are_imported(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'test.module',
            'tabs' => [
                [
                    'DIV' => 'tab1',
                    'TAB' => 'Tab 1',
                    'GROUPS' => [
                        [
                            'TITLE' => 'Group A',
                            'OPTIONS' => [
                                'MY_TEXT' => ['TYPE' => 'STRING', 'TITLE' => 'Text'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = $migrator->convert($config);

        $imports = $result->imports;
        // Should only have Form, Tab, Heading (from group title), Text
        $this->assertCount(4, $imports);
        $this->assertContains('use Gelion\BitrixOptions\Form;', $imports);
        $this->assertContains('use Gelion\BitrixOptions\Tab;', $imports);
        $this->assertContains('use Gelion\BitrixOptions\Fields\Heading;', $imports);
        $this->assertContains('use Gelion\BitrixOptions\Fields\Text;', $imports);
        // Should NOT have any other field classes
        $this->assertNotContains('use Gelion\BitrixOptions\Fields\Number;', $imports);
        $this->assertNotContains('use Gelion\BitrixOptions\Fields\Checkbox;', $imports);
        $this->assertNotContains('use Gelion\BitrixOptions\Enums\TagColor;', $imports);
    }

    public function test_multi_tab_generates_multiple_tab_entries(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'test.module',
            'tabs' => [
                [
                    'DIV' => 'first',
                    'TAB' => 'First Tab',
                    'OPTIONS' => [
                        'FIELD_A' => ['TYPE' => 'STRING', 'TITLE' => 'Field A'],
                    ],
                ],
                [
                    'DIV' => 'second',
                    'TAB' => 'Second Tab',
                    'OPTIONS' => [
                        'FIELD_B' => ['TYPE' => 'NUMBER', 'TITLE' => 'Field B'],
                    ],
                ],
            ],
        ];
        $result = $migrator->convert($config);
        $code = $result->code;

        $this->assertTrue($result->success);
        $this->assertStringContainsString("Tab::make('first', 'First Tab'", $code);
        $this->assertStringContainsString("Tab::make('second', 'Second Tab'", $code);
        $this->assertStringContainsString("Text::make('FIELD_A'", $code);
        $this->assertStringContainsString("Number::make('FIELD_B'", $code);

        $tempFile = tempnam(sys_get_temp_dir(), 'migrator_multi_tab_');
        file_put_contents($tempFile, $code);
        $output = [];
        $exitCode = 0;
        exec('php -l ' . escapeshellarg($tempFile) . ' 2>&1', $output, $exitCode);
        unlink($tempFile);
        $this->assertSame(0, $exitCode, 'Multi-tab generated code has syntax errors');
    }

    public function test_code_passes_psr12_syntax(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'test.module',
            'tabs' => [
                [
                    'DIV' => 'main',
                    'TAB' => 'Main Settings',
                    'GROUPS' => [
                        [
                            'TITLE' => 'General',
                            'OPTIONS' => [
                                'SITE_NAME' => ['TYPE' => 'STRING', 'TITLE' => 'Site Name'],
                                'ENABLE_CACHE' => ['TYPE' => 'CHECKBOX', 'TITLE' => 'Enable Cache'],
                            ],
                        ],
                    ],
                ],
                [
                    'DIV' => 'advanced',
                    'TAB' => 'Advanced',
                    'OPTIONS' => [
                        'MODE' => ['TYPE' => 'DROPDOWN', 'TITLE' => 'Mode'],
                        'ALERT_MSG' => ['TYPE' => 'ALERT', 'TITLE' => 'Info message'],
                    ],
                ],
            ],
        ];
        $result = $migrator->convert($config);

        $this->assertTrue($result->success);
        $code = $result->code;

        $this->assertStringStartsWith('<?php', $code);
        $this->assertStringContainsString("declare(strict_types=1);", $code);
        $this->assertStringContainsString('Form::make(', $code);
        $this->assertStringContainsString("Tab::make('main', 'Main Settings'", $code);
        $this->assertStringContainsString("Tab::make('advanced', 'Advanced'", $code);

        $tempFile = tempnam(sys_get_temp_dir(), 'migrator_psr12_');
        file_put_contents($tempFile, $code);
        $output = [];
        $exitCode = 0;
        exec('php -l ' . escapeshellarg($tempFile) . ' 2>&1', $output, $exitCode);
        unlink($tempFile);
        $this->assertSame(0, $exitCode, 'Generated code has syntax errors');
    }

    public function test_fluent_chains_are_properly_formatted(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'OPTION', [
            'TYPE' => 'STRING',
            'TITLE' => 'Option',
            'DEFAULT' => 'val',
            'NOTES' => 'hint',
            'PLACEHOLDER' => 'ph',
        ], $usedClasses);

        // With 3 methods, should be multi-line with 12-space indent (4 for array + 4 for continuation)
        $this->assertStringContainsString("\n", $result);
        $this->assertStringContainsString("            ->setDefault('val')", $result);
        $this->assertStringContainsString("            ->setHint('hint')", $result);
        $this->assertStringContainsString("            ->setPlaceholder('ph')", $result);
        // Verify order preserved
        $this->assertTrue(
            strpos($result, 'setDefault') < strpos($result, 'setHint')
        );
        $this->assertTrue(
            strpos($result, 'setHint') < strpos($result, 'setPlaceholder')
        );
    }

    public function test_single_method_stays_on_one_line(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'OPTION', [
            'TYPE' => 'STRING',
            'TITLE' => 'Option',
            'DEFAULT' => 'my_value',
        ], $usedClasses);

        $this->assertStringNotContainsString("\n", $result);
        $this->assertSame("Text::make('OPTION', 'Option')->setDefault('my_value'),", $result);
    }

    public function test_no_methods_compact(): void
    {
        $migrator = $this->createMigrator();
        $usedClasses = [];

        $result = $this->invokeConvertField($migrator, 'OPTION', [
            'TYPE' => 'STRING',
            'TITLE' => 'Option',
        ], $usedClasses);

        $this->assertStringNotContainsString("\n", $result);
        $this->assertSame("Text::make('OPTION', 'Option'),", $result);
    }

    public function test_integration_end_to_end(): void
    {
        $migrator = $this->createMigrator();
        $config = [
            'module_id' => 'my.module',
            'tabs' => [
                [
                    'DIV' => 'general',
                    'TAB' => 'General Settings',
                    'GROUPS' => [
                        [
                            'TITLE' => 'Basic Configuration',
                            'OPTIONS' => [
                                'SITE_NAME' => [
                                    'TYPE' => 'STRING',
                                    'TITLE' => 'Site Name',
                                    'DEFAULT' => 'My Site',
                                    'NOTES' => 'The name of your site',
                                    'PLACEHOLDER' => 'Enter site name',
                                ],
                                'MAX_ITEMS' => [
                                    'TYPE' => 'NUMBER',
                                    'TITLE' => 'Max Items',
                                    'DEFAULT' => '10',
                                    'MIN' => 0,
                                    'MAX' => 100,
                                ],
                                'ENABLE_CACHE' => [
                                    'TYPE' => 'CHECKBOX',
                                    'TITLE' => 'Enable Cache',
                                ],
                            ],
                        ],
                        [
                            'OPTIONS' => [
                                'THEME' => [
                                    'TYPE' => 'DROPDOWN',
                                    'TITLE' => 'Theme',
                                    'OPTIONS' => [
                                        ['TITLE' => 'Light', 'VALUE' => 'light'],
                                        ['TITLE' => 'Dark', 'VALUE' => 'dark'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'DIV' => 'advanced',
                    'TAB' => 'Advanced',
                    'OPTIONS' => [
                        'FEATURE_FLAG' => [
                            'TYPE' => 'STRING',
                            'TITLE' => 'Feature Flag',
                            'TAG' => 'beta',
                            'TAGCOLOR' => 'warning',
                        ],
                        'INFO_ALERT' => [
                            'TYPE' => 'ALERT',
                            'TITLE' => 'Changes require restart',
                        ],
                    ],
                ],
            ],
        ];
        $result = $migrator->convert($config);

        $this->assertTrue($result->success);
        $code = $result->code;

        // PSR-12 structure
        $this->assertStringStartsWith("<?php\n", $code);
        $this->assertStringContainsString("declare(strict_types=1);", $code);

        // Imports grouped: main → fields → enums
        $importSection = substr($code, 0, strpos($code, 'Form::make('));
        $formPos = strpos($importSection, 'use Gelion\BitrixOptions\Form;');
        $tabPos = strpos($importSection, 'use Gelion\BitrixOptions\Tab;');
        $textPos = strpos($importSection, 'use Gelion\BitrixOptions\Fields\Text;');
        $numberPos = strpos($importSection, 'use Gelion\BitrixOptions\Fields\Number;');
        $headingPos = strpos($importSection, 'use Gelion\BitrixOptions\Fields\Heading;');
        $checkboxPos = strpos($importSection, 'use Gelion\BitrixOptions\Fields\Checkbox;');
        $selectPos = strpos($importSection, 'use Gelion\BitrixOptions\Fields\Select;');
        $alertPos = strpos($importSection, 'use Gelion\BitrixOptions\Fields\Alert;');
        $tagColorPos = strpos($importSection, 'use Gelion\BitrixOptions\Enums\TagColor;');

        // Main group should come first
        $this->assertNotFalse($formPos);
        $this->assertNotFalse($tabPos);
        $this->assertTrue($formPos < $textPos, 'Form should come before Text');
        $this->assertTrue($tabPos < $textPos, 'Tab should come before Text');

        // Fields group sorted alphabetically
        $this->assertTrue($alertPos < $checkboxPos, 'Alert before Checkbox');
        $this->assertTrue($checkboxPos < $headingPos, 'Checkbox before Heading');
        $this->assertTrue($headingPos < $numberPos, 'Heading before Number');
        $this->assertTrue($numberPos < $selectPos, 'Number before Select');
        $this->assertTrue($selectPos < $textPos, 'Select before Text');

        // Enums after fields
        $this->assertTrue($textPos < $tagColorPos, 'Fields before Enums');

        // Multi-tab
        $this->assertStringContainsString("Tab::make('general', 'General Settings'", $code);
        $this->assertStringContainsString("Tab::make('advanced', 'Advanced'", $code);

        // Fluent chains with 3+ params are multi-line with proper indentation
        $this->assertStringContainsString("\n            ->setDefault('My Site')", $code);
        $this->assertStringContainsString("\n            ->setHint('The name of your site')", $code);
        $this->assertStringContainsString("\n            ->setPlaceholder('Enter site name')", $code);

        // Type-specific params multi-line
        $this->assertStringContainsString("\n            ->min(0)", $code);
        $this->assertStringContainsString("\n            ->max(100)", $code);

        // Single method (< 100 chars) stays on make() line
        $this->assertStringContainsString("->options(['light' => 'Light', 'dark' => 'Dark'])", $code);

        // Single method (> 100 chars) goes multi-line
        $this->assertStringContainsString("\n            ->setTag('beta', \\Gelion\\BitrixOptions\\Enums\\TagColor::WARNING)", $code);

        // Verify PHP syntax
        $tempFile = tempnam(sys_get_temp_dir(), 'migrator_e2e_');
        file_put_contents($tempFile, $code);
        $output = [];
        $exitCode = 0;
        exec('php -l ' . escapeshellarg($tempFile) . ' 2>&1', $output, $exitCode);
        unlink($tempFile);

        $this->assertSame(0, $exitCode, 'End-to-end generated code has syntax errors: ' . implode("\n", $output));
    }

    /**
     * Helper: assert that the migrator has a warning of the given type.
     */
    private function assertHasWarning(\Gelion\BitrixOptionsMigration\Migrator $migrator, string $expectedType): void
    {
        $warnings = $migrator->getWarnings();
        $types = array_column($warnings, 'type');
        $this->assertContains($expectedType, $types, "Expected warning type '{$expectedType}' not found");
    }
}
