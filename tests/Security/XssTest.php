<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Tests\Security {

    use Gelion\BitrixOptions\Fields\Alert;
    use Gelion\BitrixOptions\Fields\Heading;
    use Gelion\BitrixOptions\Fields\Select;
    use Gelion\BitrixOptions\Fields\Text;
    use Gelion\BitrixOptions\Fields\Textarea;
    use Gelion\BitrixOptions\Form;
    use Gelion\BitrixOptions\Tab;
    use PHPUnit\Framework\TestCase;

    final class XssTest extends TestCase
    {
        private const XSS_PAYLOAD = '"><script>alert(1)</script>';

        protected function tearDown(): void
        {
            \Bitrix\Main\Application::reset();
            \Bitrix\Main\Config\Option::reset();
        }

        public function test_text_escapes_xss_in_value(): void
        {
            $text = Text::make('TEST_KEY', 'Test Title');
            $text->moduleId = 'test.module';
            $text->value = self::XSS_PAYLOAD;

            $output = $text->view();

            $this->assertStringContainsString('&quot;', $output);
            $this->assertStringContainsString('&gt;', $output);
            $this->assertStringContainsString('&lt;script&gt;', $output);
            $this->assertStringNotContainsString(self::XSS_PAYLOAD, $output);
        }

        public function test_text_escapes_xss_in_title(): void
        {
            $text = Text::make('TEST_KEY', self::XSS_PAYLOAD);
            $text->moduleId = 'test.module';

            $output = $text->view();

            $this->assertStringContainsString('&quot;', $output);
            $this->assertStringContainsString('&gt;', $output);
            $this->assertStringContainsString('&lt;script&gt;', $output);
            $this->assertStringNotContainsString(self::XSS_PAYLOAD, $output);
        }

        public function test_select_escapes_xss_in_options(): void
        {
            $select = Select::make('TEST_KEY', 'Test Title');
            $select->moduleId = 'test.module';
            $select->options([
                self::XSS_PAYLOAD => '<img onerror=alert(1) src=x>',
            ]);

            $output = $select->view();

            // Key escaped
            $this->assertStringContainsString('&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;', $output);
            $this->assertStringNotContainsString(self::XSS_PAYLOAD, $output);

            // Title escaped
            $this->assertStringContainsString('&lt;img onerror=alert(1) src=x&gt;', $output);
            $this->assertStringNotContainsString('<img onerror=alert(1) src=x>', $output);
        }

        public function test_textarea_escapes_xss_content(): void
        {
            $textarea = Textarea::make('TEST_KEY', 'Test Title');
            $textarea->moduleId = 'test.module';
            $textarea->value = self::XSS_PAYLOAD;

            $output = $textarea->view();

            $this->assertStringContainsString('&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;', $output);
            $this->assertStringNotContainsString(self::XSS_PAYLOAD, $output);
        }

        public function test_alert_escapes_xss_in_title(): void
        {
            $alert = Alert::make(self::XSS_PAYLOAD);

            $output = $alert->view();

            // Verify the payload is escaped, not raw
            $this->assertStringNotContainsString(self::XSS_PAYLOAD, $output);
            $this->assertStringContainsString('&quot;&gt;&lt;script&gt;', $output);
            $this->assertStringNotContainsString('htmlspecialchars', $output);
        }

        public function test_heading_escapes_xss_in_title(): void
        {
            $heading = Heading::make(self::XSS_PAYLOAD);

            $output = $heading->view();

            // Verify the payload is escaped, not raw
            $this->assertStringNotContainsString(self::XSS_PAYLOAD, $output);
            $this->assertStringContainsString('&quot;&gt;&lt;script&gt;', $output);
            $this->assertStringNotContainsString('htmlspecialchars', $output);
        }

        public function test_alert_with_allow_html_passes_xss_raw(): void
        {
            $alert = Alert::make(self::XSS_PAYLOAD)->allowHtml();
            $output = $alert->view();
            // allowHtml() intentionally passes raw — developer opted in
            $this->assertStringContainsString(self::XSS_PAYLOAD, $output);
        }

        public function test_heading_with_allow_html_passes_xss_raw(): void
        {
            $heading = Heading::make(self::XSS_PAYLOAD)->allowHtml();
            $output = $heading->view();
            // allowHtml() intentionally passes raw — developer opted in
            $this->assertStringContainsString(self::XSS_PAYLOAD, $output);
        }

        public function test_form_escapes_module_id(): void
        {
            $GLOBALS['APPLICATION'] = new class {
                public function GetCurPage(): string
                {
                    return '/bitrix/admin/settings.php';
                }
            };

            $tab = Tab::make('tab1', 'Tab 1', [
                Text::make('KEY', 'Title'),
            ]);

            $xssModuleId = '"><script>alert(1)</script>';

            ob_start();
            Form::make($xssModuleId, [$tab]);
            $output = ob_get_clean();

            $this->assertStringContainsString('&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;', $output);
            $this->assertStringNotContainsString($xssModuleId, $output);
        }
    }
}
