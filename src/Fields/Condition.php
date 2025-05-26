<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Fields;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use CCatalogCondTree;
use Gelion\BitrixOptions\Traits\WithModuleId;

class Condition extends Text
{
    use WithModuleId;

    public function view(): string
    {
        $tree = new CCatalogCondTree();
        $status = $tree->Init(
            BT_COND_MODE_DEFAULT,
            BT_COND_BUILD_CATALOG,
            [
                'FORM_ID' => $this->moduleId,
                'FORM_NAME' => str_replace('.', '_', $this->moduleId),
                'CONT_ID' => "{$this->key}_condition",
                'JS_NAME' => "JSCatCond_{$this->key}",
                'PREFIX' => $this->key,
            ]
        );

        ob_start();
        if ($status) {
            $tree->Show($this->value);
        }

        $condition = ob_get_clean();

        return <<<HTML
            <tr>
                <td>
                    $this->title
                    {$this->getHintView()}
                </td>
                <td nowrap>
                    <div id="$this->key-block">
                        <div class="$this->key-container" id="{$this->key}_condition">
                            $condition
                        </div>
                    </div>
                </td>
            </tr>
        HTML;
    }

    public function saveFromRequest(): static
    {
        if (!check_bitrix_sessid()) return $this;

        $request = Application::getInstance()->getContext()->getRequest();
        if ($request->get('update') !== 'Y') return $this;

        $moduleId = $request->get('mid');
        if (!$moduleId) return $this;

        $value = $request->get($this->key);
        if (!$value) return $this;

        $tree = new CCatalogCondTree();
        $status = $tree->Init(BT_COND_MODE_PARSE, BT_COND_BUILD_CATALOG, [
            'PREFIX' => $this->key,
        ]);
        $conditions = $status ? $tree->Parse() : [];
        $value = base64_encode(serialize($conditions));

        Option::set($moduleId, $this->key, $value);

        return $this;
    }

    public function fillValue(): static
    {
        $value = Option::get($this->moduleId, $this->key, $this->default);

        $this->value = is_null($value) ?
            [] : unserialize(base64_decode($value));

        return $this;
    }
}



