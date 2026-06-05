<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Tests;

use Gelion\BitrixOptions\Fields\Text;
use Gelion\BitrixOptions\Tab;
use PHPUnit\Framework\TestCase;

final class TabTest extends TestCase
{
    protected function tearDown(): void
    {
        \Bitrix\Main\Application::reset();
        \Bitrix\Main\Config\Option::reset();
    }

    public function test_make_creates_instance(): void
    {
        $tab = Tab::make('tab1', 'Tab 1', []);

        $this->assertInstanceOf(Tab::class, $tab);
    }

    public function test_make_sets_id_title_fields(): void
    {
        $tab = Tab::make('tab1', 'Tab 1', [Text::make('k')]);

        $this->assertSame('tab1', $tab->id);
        $this->assertSame('Tab 1', $tab->title);
        $this->assertCount(1, $tab->fields);
    }

    public function test_view_returns_string(): void
    {
        $tab = Tab::make('tab1', 'Tab 1', [Text::make('k', 'Title')]);
        $output = $tab->view('test.module');

        $this->assertIsString($output);
        $this->assertNotEmpty($output);
    }

    public function test_view_returns_empty_with_no_fields(): void
    {
        $tab = Tab::make('tab1', 'Tab 1', []);
        $output = $tab->view('test.module');

        $this->assertSame('', $output);
    }
}
