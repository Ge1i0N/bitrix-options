<?php

declare(strict_types=1);

namespace Gelion\BitrixOptions;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use CAdminTabControl;

Extension::load('ui.buttons');
Extension::load('ui.forms');
Extension::load('ui.hint');
Extension::load('ui.alerts');

Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/options.php');

class Form
{
    public function __construct(
        public string $moduleId,
        public array $tabs,
    ) {
    }

    public static function make(string $moduleId, array $tabs): void
    {
        $instance = new static($moduleId, $tabs);
        $instance->view();
    }

    private function view(): void
    {
        if (count($this->tabs) > 0) {
            $tabControl = new CAdminTabControl('tabControl', $this->getTabsFormated($this->tabs));
            print $this->displayFormHeader();
            $tabControl->Begin();
            $tabControl->Buttons();
            $tabControl->End();
            print $this->displayFormFooter();
        }
    }

    private function getTabsFormated(array $tabs): array
    {
        $result = [];

        foreach ($tabs as $tab) {
            $result[] = [
                'DIV' => $tab->id,
                'TAB' => $tab->title,
                'TITLE' => '',
                'ONSELECT' => '',
                'CONTENT' => $tab->view($this->moduleId),
            ];
        }

        return $result;
    }

    public function displayFormHeader(): string
    {
        global $APPLICATION;

        $formName = str_replace('.', '_', $this->moduleId);
        $action = sprintf(
            '%s?mid=%s&lang=%s',
            $APPLICATION->GetCurPage(),
            $this->moduleId,
            LANGUAGE_ID
        );

        $result = <<<HTML
            <form   id="$this->moduleId"
                    name="$formName"
                    method="POST"
                    action="$action"
                    enctype="multipart/form-data">
            HTML;
        $result .= bitrix_sessid_post();

        return $result;
    }

    private function displayFormFooter(): string
    {
        $applyText = Loc::getMessage('MAIN_OPT_APPLY');

        return <<<HTML
            <div class="ui-btn-container ui-btn-container-left">
                <button type="submit" class="ui-btn ui-btn-success" name="apply">$applyText</button>
                <input type="hidden" name="update" value="Y" />
            </div>
            </form>
            <script>
                BX.UI.Hint.init(BX('container'))
            </script>
        HTML;
    }
}
