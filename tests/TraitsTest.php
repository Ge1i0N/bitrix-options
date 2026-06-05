<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Tests;

use Gelion\BitrixOptions\Enums\Color;
use Gelion\BitrixOptions\Enums\TagColor;
use PHPUnit\Framework\TestCase;

final class TraitsTest extends TestCase
{
    public function test_hint_getHintView_escapes_xss_script_tags(): void
    {
        $obj = new class {
            use \Gelion\BitrixOptions\Traits\WithHint;

            public function exposeHintView(): string
            {
                return $this->getHintView();
            }
        };

        $obj->setHint('<script>alert("xss")</script>');
        $html = $obj->exposeHintView();

        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringContainsString('&quot;', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_hint_getHintView_uses_htmlspecialchars(): void
    {
        $obj = new class {
            use \Gelion\BitrixOptions\Traits\WithHint;

            public function exposeHintView(): string
            {
                return $this->getHintView();
            }
        };

        $obj->setHint('"><script>');
        $html = $obj->exposeHintView();

        $this->assertStringContainsString('&quot;', $html);
        $this->assertStringContainsString('&gt;', $html);
        $this->assertStringContainsString('&lt;', $html);
    }

    public function test_hint_getHintView_returns_empty_when_no_hint(): void
    {
        $obj = new class {
            use \Gelion\BitrixOptions\Traits\WithHint;

            public function exposeHintView(): string
            {
                return $this->getHintView();
            }
        };

        $this->assertSame('', $obj->exposeHintView());
    }

    public function test_tag_getTagView_contains_tag_text(): void
    {
        $obj = new class {
            use \Gelion\BitrixOptions\Traits\WithTag;

            public function exposeTagView(): string
            {
                return $this->getTagView();
            }
        };

        $obj->setTag('mytag');
        $html = $obj->exposeTagView();

        $this->assertStringContainsString('mytag', $html);
    }

    public function test_tag_getTagView_contains_color_class(): void
    {
        $obj = new class {
            use \Gelion\BitrixOptions\Traits\WithTag;

            public function exposeTagView(): string
            {
                return $this->getTagView();
            }
        };

        $obj->setTag('tag', TagColor::DANGER);
        $html = $obj->exposeTagView();

        $this->assertStringContainsString('ui-ctl-tag-danger', $html);
    }

    public function test_tag_getTagView_returns_empty_when_no_tag(): void
    {
        $obj = new class {
            use \Gelion\BitrixOptions\Traits\WithTag;

            public function exposeTagView(): string
            {
                return $this->getTagView();
            }
        };

        $this->assertSame('', $obj->exposeTagView());
    }

    public function test_color_setColor_returns_self(): void
    {
        $obj = new class {
            use \Gelion\BitrixOptions\Traits\WithColor;
        };

        $result = $obj->setColor(Color::SUCCESS);

        $this->assertSame($obj, $result);
    }

    public function test_module_setModuleId_returns_self(): void
    {
        $obj = new class {
            use \Gelion\BitrixOptions\Traits\WithModuleId;
        };

        $result = $obj->setModuleId('test.module');

        $this->assertSame($obj, $result);
    }
}
