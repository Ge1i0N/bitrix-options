<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Tests;

use Gelion\BitrixOptions\Fields\Text;
use Gelion\BitrixOptions\Form;
use Gelion\BitrixOptions\Tab;
use PHPUnit\Framework\TestCase;

final class FormTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['APPLICATION'] = new class {
            public function GetCurPage(): string
            {
                return '/bitrix/admin/settings.php';
            }
        };
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['APPLICATION']);
        \Bitrix\Main\Application::reset();
        \Bitrix\Main\Config\Option::reset();
    }

    public function test_form_with_one_tab_renders_form(): void
    {
        ob_start();
        Form::make('test.module', [Tab::make('tab1', 'Tab 1', [Text::make('KEY', 'Title')])]);
        $output = ob_get_clean();

        $this->assertStringContainsString('<form', $output);
        $this->assertStringContainsString('test.module', $output);
        $this->assertStringContainsString('submit', $output);
        $this->assertStringContainsString('update', $output);
        $this->assertStringContainsString('Y', $output);
    }

    public function test_form_with_no_tabs_returns_empty(): void
    {
        ob_start();
        Form::make('test.module', []);
        $output = ob_get_clean();

        $this->assertSame('', $output);
    }

    public function test_form_escapes_module_id_in_html(): void
    {
        $xss = '"><script>alert(1)</script>';

        ob_start();
        Form::make($xss, [Tab::make('tab1', 'Tab 1', [Text::make('KEY')])]);
        $output = ob_get_clean();

        $this->assertStringNotContainsString($xss, $output);
        $this->assertStringContainsString('&quot;', $output);
    }

    public function test_form_contains_tab_content(): void
    {
        ob_start();
        Form::make('test.module', [Tab::make('tab1', 'Tab 1', [Text::make('KEY', 'Title')])]);
        $output = ob_get_clean();

        $this->assertStringContainsString('name="KEY"', $output);
        $this->assertStringContainsString('Title', $output);
    }

    public function test_form_contains_apply_button(): void
    {
        ob_start();
        Form::make('test.module', [Tab::make('tab1', 'Tab 1', [Text::make('KEY')])]);
        $output = ob_get_clean();

        $this->assertStringContainsString('apply', $output);
        $this->assertStringContainsString('MAIN_OPT_APPLY', $output);
    }

    public function test_form_contains_hidden_update_field(): void
    {
        ob_start();
        Form::make('test.module', [Tab::make('tab1', 'Tab 1', [Text::make('KEY')])]);
        $output = ob_get_clean();

        $this->assertStringContainsString('<input type="hidden" name="update" value="Y" />', $output);
    }

    public function test_displayFormHeader_returns_form_open_tag(): void
    {
        $reflection = new \ReflectionMethod(Form::class, 'displayFormHeader');
        $result = $reflection->invoke(new Form('test.module', [Tab::make('t', 'T', [Text::make('k')])]));
        $this->assertStringContainsString('<form', $result);
        $this->assertStringContainsString('method="POST"', $result);
        $this->assertStringContainsString('test.module', $result);
    }

    public function test_form_with_multiple_tabs(): void
    {
        ob_start();
        Form::make('test.module', [
            Tab::make('tab1', 'Tab 1', [Text::make('KEY1')]),
            Tab::make('tab2', 'Tab 2', [Text::make('KEY2')]),
        ]);
        $output = ob_get_clean();

        $this->assertStringContainsString('tab1', $output);
        $this->assertStringContainsString('tab2', $output);
    }

    public function test_form_contains_container_for_hint_init(): void
    {
        ob_start();
        Form::make('test.module', [Tab::make('tab1', 'Tab 1', [Text::make('KEY')])]);
        $output = ob_get_clean();

        $this->assertStringContainsString('BX.UI.Hint.init', $output);
    }

    public function test_form_header_contains_action_url(): void
    {
        $reflection = new \ReflectionMethod(Form::class, 'displayFormHeader');
        $result = $reflection->invoke(new Form('test.module', []));

        $this->assertStringContainsString('/bitrix/admin/settings.php', $result);
    }
}
