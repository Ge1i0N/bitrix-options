<?php

namespace Gelion\BitrixOptions;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI;
use CAdminTabControl;
use Gelion\BitrixOptions\Types;

UI\Extension::load('ui.forms');
UI\Extension::load('ui.buttons');
UI\Extension::load('ui.hint');

Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/options.php');

class Form
{
    public $optionValues = [];

    private $moduleId = '';
    private $tabs = [];
    private $options = [];

    private static $types = [
        'CHECKBOX' => Types\Checkbox::class,
        'COLORPICKER' => Types\Colorpicker::class,
        'DROPDOWN' => Types\Dropdown::class,
        'MULTISELECT' => Types\MultiSelect::class,
        'NUMBER' => Types\Number::class,
        'STRING' => Types\Text::class,
        'TEXTAREA' => Types\Textarea::class,
        'HTMLEDITOR' => Types\HtmlEditor::class,
    ];

    public static function typesRegister(array $array): void
    {
        static::$types = array_merge(static::$types, $array);
    }

    public static function generate($moduleId, $tabs): void
    {
        $instance = new static($moduleId, $tabs);
        $instance->show();
    }

    public function __construct($moduleId, $tabs)
    {
        $this->moduleId = $moduleId;
        $this->tabs = $tabs;
        $this->options = $this->getOptions();

        if ($_REQUEST['update'] == 'Y' && check_bitrix_sessid()) {
            $this->saveOptions();
        }

        $this->setCurrentValues();
    }

    private function getOptions(): array
    {
        $options = [];
        foreach ($this->tabs as $tab)
            foreach ($tab['GROUPS'] as $group)
                foreach ($group['OPTIONS'] as $name => $option)
                    $options[$name] = $option;

        return $options;
    }

    private function saveOptions(): void
    {
        foreach ($this->options as $name => $option) {
            $value = $_REQUEST[$name];

            if ($option['TYPE'] == 'CHECKBOX' && $value !== 'Y')
                $value = 'N';
            elseif ($option['TYPE'] == 'HTMLEDITOR') {
                $type = $_REQUEST[$name . '_TYPE'];
                if (!in_array($type, ['text', 'html']))
                    $type = 'html';

                $value = serialize(['type' => $type, 'value' => $value]);
            } elseif (is_array($value))
                $value = serialize($value);

            Option::set($this->moduleId, $name, $value);
        }
    }

    private function setCurrentValues(): void
    {
        foreach ($this->options as $name => $option) {
            $this->optionValues[$name] = Option::get($this->moduleId, $name, $option['FIELDS']['DEFAULT']);
            if (in_array($option['TYPE'], ['MULTISELECT'])) {
                $this->optionValues[$name] = unserialize($this->optionValues[$name]);
            }
        }
    }

    private function getTabsFormated(): array
    {
        $properties = [];

        foreach ($this->tabs as $tabId => $tab)
            foreach ($tab['GROUPS'] as $groupName => $group) {
                $properties[$tabId][$groupName] = [];

                foreach ($group['OPTIONS'] as $option => $property) {
                    if ($property['SORT'] < 0 || !isset($property['SORT']))
                        $property['SORT'] = 0;

                    $fields = array_merge(
                        $property['FIELDS'],
                        [
                            'VALUE' => $this->optionValues[$option],
                            'NAME' => $option,
                        ]
                    );

                    if (array_key_exists($property['TYPE'], static::$types)) {
                        $form = new static::$types[$property['TYPE']];
                    } else {
                        $form = new Types\Text();
                    }

                    $form->setFields($fields);
                    $form->setParams($property['PARAMS']);

                    $input = $form->getHtml();


                    $note = '';
                    if (isset($property['FIELDS']['NOTES']) && $property['FIELDS']['NOTES'] != '')
                        $note = '<span data-hint="' . htmlspecialchars($property['FIELDS']['NOTES']) . '"></span>';

                    $properties[$tabId][$groupName]['TITLE'] = $group['TITLE'];
                    $properties[$tabId][$groupName]['OPTIONS'][] = '<tr><td valign="top">' . $property['FIELDS']['TITLE'] . $note . '</td><td nowrap>' . $input . '</td></tr>';
                    $properties[$tabId][$groupName]['OPTIONS_SORT'][] = $property['SORT'];
                }
            }

        return $properties;
    }

    private function show(): void
    {
        global $APPLICATION;

        $tabs = $this->getTabsFormated();

        if (count($tabs) > 0) {
            $tabControl = new CAdminTabControl('tabControl', $this->tabs);
            $tabControl->Begin();
            $language = LANGUAGE_ID;

            print <<<HTML
                <form id="{$this->moduleId}" name="{$this->moduleId}" method="POST" action="{$APPLICATION->GetCurPage()}?mid={$this->moduleId}&lang={$language}" enctype="multipart/form-data">
            HTML;
            print bitrix_sessid_post();

            foreach ($tabs as $groups) {
                $tabControl->BeginNextTab();

                foreach ($groups as $groupId => $group) {
                    if (count($group['OPTIONS_SORT']) > 0) {
                        print <<<HTML
                            <tr class="heading"><td colspan="2"><b>{$group['TITLE']}</b></td></tr>
                        HTML;

                        array_multisort($group['OPTIONS_SORT'], $group['OPTIONS']);

                        foreach ($group['OPTIONS'] as $option)
                            print $option;
                    }
                }
            }

            $tabControl->Buttons();

            $applyText = Loc::getMessage('MAIN_OPT_APPLY');
            print <<<HTML
                <div class="ui-btn-container ui-btn-container-left">
                    <button type="submit" class="ui-btn ui-btn-success" name="apply">{$applyText}</button>
                    <input type="hidden" name="update" value="Y" />
                </div>
                </form>
                <script>BX.UI.Hint.init(BX('container'))</script>
                <style>
                input.ui-ctl-element[type="text"],
                input.ui-ctl-element[type="number"],
                select.ui-ctl-element {
                    margin: 0;
                    padding: 0 11px;
                }
                </style>
            HTML;
            $tabControl->End();
        }
    }
}
