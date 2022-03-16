<?php

namespace Gelion\BitrixOptions;

abstract class TypeForm extends TypeBase
{
    public $fields;
    public $params;

    private $display = [
        'inline',
        'block',
    ];

    private $width = [
        25,
        33,
        50,
        75,
        100,
        'wa',
        'wd',
    ];

    private $height = [
        'xs',
        'sm',
        'md',
        'lg',
    ];

    private $color = [
        'default',
        'active',
        'success',
        'warning',
        'danger',
        'disabled',
    ];

    private $tagColor = [
        'default',
        'success',
        'primary',
        'warning',
        'danger',
    ];

    private $modificator = [
        'no-border',
        'no-padding',
        'underline',
        'round',
    ];

    public function __construct()
    {
        $this->setDefault();
    }

    public function setDefault(): void
    {
        $this->params = [
            'DISPLAY' => 'block',
            'WIDTH' => 'wd',
            'HEIGHT' => 'md',
            'COLOR' => 'default',
            'TAGCOLOR' => 'default',
            'MODIFICATOR' => false,
        ];
    }

    public function getClass(): string
    {
        $modificator = $this->params['MODIFICATOR'] ? "ui-ctl-{$this->params['MODIFICATOR']}" : '';

        return <<<CLASS
            ui-ctl-{$this->params['DISPLAY']}
            ui-ctl-{$this->params['WIDTH']}
            ui-ctl-{$this->params['HEIGHT']}
            ui-ctl-{$this->params['COLOR']}
            {$modificator}
        CLASS;
    }

    public function getTag(): string
    {
        if ($this->params['TAGCOLOR'] === 'default')
            $class = 'ui-ctl-tag';
        else
            $class = "ui-ctl-tag ui-ctl-tag-{$this->params['TAGCOLOR']}";

        if ($this->fields['TAG'])
            return <<<HTML
            <div class="{$class}">{$this->fields['TAG']}</div>
        HTML;
        else
            return '';
    }

    public function getHtml(): string
    {
        return '';
    }
}
