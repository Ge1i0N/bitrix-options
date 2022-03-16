<?php

namespace Gelion\BitrixOptions\Types;

use CCatalogCondTree;
use Gelion\BitrixOptions\TypeBase;

class Conditions extends TypeBase
{
    public function getHtml(): string
    {
        $name = htmlspecialchars($this->fields['NAME']);
        $value = $this->fields['VALUE'];

        $tree = new CCatalogCondTree();
        $status = $tree->Init(
            BT_COND_MODE_DEFAULT,
            BT_COND_BUILD_CATALOG,
            [
                'FORM_NAME' => $this->fields['FORM_NAME'],
                'CONT_ID' => "{$name}_condition",
                'JS_NAME' => "JSCatCond_{$name}",
                'PREFIX' => $name,
            ]
        );

        ob_start();
        if ($status)
            $tree->Show($value);

        $script = ob_get_clean();

        return <<<HTML
            <div id="{$name}_condition">{$script}</div>
        HTML;
    }

    public function parseValueString()
    {
        return is_null($this->fields['VALUE']) ?
            [] : unserialize(base64_decode($this->fields['VALUE']));
    }

    public function getValueString(): string
    {
        $tree = new CCatalogCondTree();
        $status = $tree->Init(BT_COND_MODE_PARSE, BT_COND_BUILD_CATALOG, [
            'PREFIX' => $this->fields['NAME'],
        ]);
        $conditions = $status ? $tree->Parse() : [];
        $value = base64_encode(serialize($conditions));

        return $value;
    }
}
