<?php

namespace Gelion\BitrixOptions\Types;

use CFileMan;
use Gelion\BitrixOptions\TypeBase;

class HtmlEditor extends TypeBase
{
    public function setDefault(): void
    {
        $this->params = [
            'WIDTH' => '100%',
            'HEIGHT' => '',
            'PHP' => true,
            'TASKBAR' => false,
        ];
    }

    public function getHtml(): string
    {
        $values = unserialize($this->fields['VALUE']);

        $type = htmlspecialchars($values['type']);
        $value = $values['value'];
        $name = htmlspecialchars($this->fields['NAME']);

        ob_start();
        CFileMan::AddHTMLEditorFrame(
            $name,
            $value,
            "{$name}_TYPE",
            $type,
            [
                'height' => $this->params['HEIGHT'],
                'width' => $this->params['WIDTH']
            ],
            'N',
            0,
            '',
            '',
            false,
            !$this->params['PHP'],
            $this->params['TASKBAR'],
        );
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public function getValueString(): string
    {
        if (!in_array($this->fields['TYPE'], ['text', 'html']))
            $this->fields['TYPE'] = 'html';

        return serialize(['type' => $this->fields['TYPE'], 'value' => $this->fields['VALUE']]);
    }
}
