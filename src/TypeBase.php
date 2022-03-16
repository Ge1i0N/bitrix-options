<?php

namespace Gelion\BitrixOptions;

abstract class TypeBase implements TypeInterface
{
    public $fields;
    public $params;

    public function __construct()
    {
        $this->setDefault();
    }

    public function setDefault(): void
    {
    }

    public function setParams($params): void
    {
        if (!empty($params))
            foreach ($params as $name => $value) {
                if (isset($this->params[$name])) {
                    $this->params[$name] = $value;
                }
            }
    }

    public function setFields($fields): void
    {
        $this->fields = array_merge([
            'TITLE' => '',
            'NAME' => '',
            'VALUE' => '',
            'PLACEHOLDER' => '',
        ], $fields);
    }

    public function getHtml(): string
    {
        return '';
    }

    public function parseValueString()
    {
        return strval($this->fields['VALUE']);
    }

    public function getValueString(): string
    {
        return strval($this->fields['VALUE']);
    }
}
