<?php

declare(strict_types=1);

/**
 * Mock for CAdminTabControl.
 * Minimal mock – outputs tab content during Begin(), no Bitrix dependency.
 */
class CAdminTabControl
{
    private array $tabs;

    public function __construct(string $id, array $tabs)
    {
        $this->tabs = $tabs;
    }

    public function Begin(): void
    {
        foreach ($this->tabs as $tab) {
            echo $tab['DIV'] ?? '';
            echo $tab['CONTENT'] ?? '';
        }
    }

    public function Buttons(): void {}

    public function End(): void {}
}
