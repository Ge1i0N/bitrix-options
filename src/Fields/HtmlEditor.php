<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions\Fields;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use CFileMan;
use Gelion\BitrixOptions\Traits\WithHint;
use Gelion\BitrixOptions\Traits\WithModuleId;

class HtmlEditor
{
    use WithModuleId, WithHint;

    public mixed $value = '';

    public function __construct(
        public string $key,
        public $title,
    ) {
    }

    public function fillValue(): static
    {
        $value = Option::get($this->moduleId, $this->key);
        $unserialized = unserialize($value);
        $this->value = $unserialized !== false ? $unserialized : ['type' => 'TEXT', 'value' => ''];

        return $this;
    }

    public static function make($key, $title = null): static
    {
        $class = new static($key, $title ?? $key);

        $class->saveFromRequest();

        return $class;
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

        $type = $request->get($this->key . '_TYPE') ?? 'TEXT';

        $serialized = serialize(['type' => $type, 'value' => $value]);

        Option::set($moduleId, $this->key, $serialized);

        return $this;
    }

    public function view(): string
    {
        ob_start();

        CFileMan::AddHTMLEditorFrame(
            $this->key,
            $this->value['value'] ?? '',
            "{$this->key}_TYPE",
            $this->value['type'] ?? 'text',
            [
                'height' => '',
                'width' => '100%',
            ],
            'N',
            0,
            '',
            '',
            false,
            false,
            false,
        );

        $editor = ob_get_clean();

        return <<<HTML
            <tr>
                <td>
                    $this->title
                    {$this->getHintView()}
                </td>
                <td nowrap>
                    <div id="$this->key-block">
                        <div class="$this->key-container">
                            $editor
                        </div>
                    </div>
                </td>
            </tr>
        HTML;
    }
}



