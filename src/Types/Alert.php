<?php

namespace Gelion\BitrixOptions\Types;

use Gelion\BitrixOptions\TypeBase;

class Alert extends TypeBase
{
    private $display = [
        'text-center',
        'inline',
    ];

    private $height = [
        'xs',
        'md',
    ];

    private $color = [
        'default',
        'active',
        'success',
        'warning',
        'danger',
        'disabled',
    ];

    private $icon = [
        'warning',
        'danger',
        'info',
    ];

    public function getHtml(): string
    {
        $class = $this->getClass();

        return <<<HTML
            <div class="ui-alert {$class}">
                <span class="ui-alert-message">{$this->fields['TITLE']}</span>
           </div>
        HTML;
    }

    public function getClass(): string
    {
        $display = $this->params['DISPLAY'] ? "ui-alert-{$this->params['DISPLAY']}" : '';
        $height = $this->params['HEIGHT'] ? "ui-alert-{$this->params['HEIGHT']}" : '';
        $color = $this->params['COLOR'] ? "ui-alert-{$this->params['COLOR']}" : '';
        $icon = $this->params['ICON'] ? "ui-alert-icon-{$this->params['ICON']}" : '';

        return <<<CLASS
            {$display}
            {$height}
            {$color}
            {$icon}
        CLASS;
    }

    public function setDefault(): void
    {
        $this->params = [
            'DISPLAY' => false,
            'HEIGHT' => false,
            'COLOR' => 'default',
            'ICON' => false,
        ];
    }
}
