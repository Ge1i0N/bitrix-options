<?php

namespace Gelion\BitrixOptions;

interface TypeInterface
{
    public function setFields($fields): void;
    public function setParams($params): void;
    public function getHtml(): string;
}
