<?php

declare(strict_types=1);

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Gelion\BitrixOptions\Enums\AlertIcon;
use Gelion\BitrixOptions\Enums\AlertSize;
use Gelion\BitrixOptions\Enums\AlertTextPosition;
use Gelion\BitrixOptions\Enums\Color;
use Gelion\BitrixOptions\Enums\Display;
use Gelion\BitrixOptions\Enums\Height;
use Gelion\BitrixOptions\Enums\Resize;
use Gelion\BitrixOptions\Enums\TagColor;
use Gelion\BitrixOptions\Enums\TextStyle;
use Gelion\BitrixOptions\Enums\Width;
use Gelion\BitrixOptions\Fields\Alert;
use Gelion\BitrixOptions\Fields\Checkbox;
use Gelion\BitrixOptions\Fields\ColorPicker;
use Gelion\BitrixOptions\Fields\Condition;
use Gelion\BitrixOptions\Fields\Date;
use Gelion\BitrixOptions\Fields\Email;
use Gelion\BitrixOptions\Fields\Heading;
use Gelion\BitrixOptions\Fields\HtmlEditor;
use Gelion\BitrixOptions\Fields\Number;
use Gelion\BitrixOptions\Fields\Select;
use Gelion\BitrixOptions\Fields\Text;
use Gelion\BitrixOptions\Fields\Textarea;
use Gelion\BitrixOptions\Form;
use Gelion\BitrixOptions\Tab;

define('DEMO_MODULE_ID', 'bitrix_options_demo');

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/vendor/autoload.php';

$request = Application::getInstance()->getContext()->getRequest();

if ($request->isPost() && check_bitrix_sessid()) {
    $restore = $request->getPost('restore') === 'Y';

    if ($restore) {
        Option::delete(DEMO_MODULE_ID);

        LocalRedirect($APPLICATION->GetCurPage() . '?mid=' . urlencode(DEMO_MODULE_ID) . '&lang=' . LANGUAGE_ID . '&restore_done=1');
    }
}

$APPLICATION->SetTitle('Демо: bitrix-options');

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';
?>

    <?php if ($request->get('restore_done') === '1'): ?>
        <?php CAdminMessage::ShowNote('Все демо-опции удалены из БД.'); ?>
    <?php endif; ?>

    <h1>Демонстрация пакета bitrix-options</h1>
    <p>Все типы полей с различными настройками</p>
    <button type="button" class="ui-btn ui-btn-light-border ui-btn-danger" onclick="resetDemoData()">
        🗑 Сбросить демо-опции
    </button>
    <br><br>
    <script>
        function resetDemoData() {
            if (!confirm('Сбросить все демо-опции?')) return;
            const form = document.querySelector('form[id="<?= DEMO_MODULE_ID ?>"]');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'restore';
            input.value = 'Y';
            form.appendChild(input);
            form.submit();
        }
    </script>
<?php
Form::make(DEMO_MODULE_ID, [

        Tab::make('tab_basic', '📋 Базовые поля', [
                Alert::make('Параметры сохраняются в Bitrix\Main\Config\Option для модуля ' . DEMO_MODULE_ID . '. Нажмите "Применить" чтобы сохранить — данные подтянутся из БД при обновлении страницы. Нажмите "Сбросить демо-данные" чтобы очистить все опции.')
                        ->setIcon(AlertIcon::INFO)
                        ->setColor(Color::ACTIVE),

                Text::make('demo_text', 'Текст:'),
                Text::make('demo_text_placeholder', 'Текст (плейсхолдер)')
                        ->setPlaceholder('Введите значение...'),

                Text::make('demo_text_default', 'Текст (значение по умолчанию)')
                        ->setDefault('значение-по-умолчанию'),

                Textarea::make('demo_textarea', 'Текстовое поле (textarea):'),
                Textarea::make('demo_textarea_rows', 'Textarea (5 строк)')
                        ->rows(5),
                Textarea::make('demo_textarea_no_resize', 'Textarea (без ресайза)')
                        ->rows(4)
                        ->setResize(Resize::NO_RESIZE),
                Textarea::make('demo_textarea_resize_y', 'Textarea (только по вертикали)')
                        ->rows(4)
                        ->setResize(Resize::RESIZE_Y),

                Number::make('demo_number', 'Число:'),
                Number::make('demo_number_range', 'Число (0-100, шаг 5)')
                        ->min(0)
                        ->max(100)
                        ->step(5),

                Checkbox::make('demo_checkbox', 'Чекбокс (да/нет):'),

                Select::make('demo_select', 'Селект (один):')
                        ->options([
                                '' => '(выберите значение)',
                                '1' => 'Первый вариант',
                                '2' => 'Второй вариант',
                                '3' => 'Третий вариант',
                        ]),
                Select::make('demo_select_multi', 'Селект (множественный)')
                        ->options([
                                'news' => 'Новости',
                                'articles' => 'Статьи',
                                'reviews' => 'Обзоры',
                                'forum' => 'Форум',
                        ])
                        ->multiple()
                        ->size(5),
        ]),

        Tab::make('tab_advanced', '⚙️ Дополнительные поля', [
                ColorPicker::make('demo_color', 'Цвет:'),
                Date::make('demo_date', 'Дата:'),
                Email::make('demo_email', 'Email:'),
                Heading::make('🎯 Заголовок — разделитель секций'),
                Alert::make('✅ Успех! <br>Данные сохранены')
                        ->setSize(AlertSize::MD),
                Alert::make('⚠️ Внимание! Это предупреждение')
                        ->setIcon(AlertIcon::WARNING)
                        ->setColor(Color::WARNING),
                Alert::make('❌ Ошибка! Что-то пошло не так')
                        ->setIcon(AlertIcon::DANGER)
                        ->setColor(Color::DANGER),
                Alert::make('ℹ️ Информационное сообщение')
                        ->setIcon(AlertIcon::INFO)
                        ->setColor(Color::ACTIVE)
                        ->setTextPosition(AlertTextPosition::CENTER),
                Alert::make('📦 Компактный алерт (XS)')
                        ->setSize(AlertSize::XS)
                        ->setIcon(AlertIcon::INFO)
                        ->setColor(Color::ACTIVE),

                HtmlEditor::make('demo_html', 'HTML-редактор:'),

                Condition::make('demo_condition', 'Условие:'),
        ]),

        Tab::make('tab_styles', '🎨 Стилизация', [
                Heading::make('🖌️ Подсказки (hint)'),
                Text::make('demo_text_hint', 'С подсказкой')
                        ->setHint('Это всплывающая подсказка'),

                Heading::make('🏷️ Теги (tag)'),
                Text::make('demo_text_tag', 'С тегом')
                        ->setTag('NEW', TagColor::PRIMARY),
                Text::make('demo_text_tag_success', 'Тег успеха')
                        ->setTag('SUCCESS', TagColor::SUCCESS),
                Text::make('demo_text_tag_danger', 'Тег ошибки')
                        ->setTag('DANGER', TagColor::DANGER),

                Heading::make('🎨 Цвета обводки'),
                Text::make('demo_text_color_default', 'Обычный')
                        ->setColor(Color::ACTIVE),
                Text::make('demo_text_color_success', 'Успех')
                        ->setColor(Color::SUCCESS),
                Text::make('demo_text_color_warning', 'Предупреждение')
                        ->setColor(Color::WARNING),
                Text::make('demo_text_color_danger', 'Ошибка')
                        ->setColor(Color::DANGER),

                Heading::make('📐 Ширина'),
                Text::make('demo_text_width_default', 'Ширина по умолчанию')
                        ->setWidth(Width::DEFAULT),
                Text::make('demo_text_width_auto', 'Авто-ширина')
                        ->setWidth(Width::AUTO),
                Text::make('demo_text_width_25', 'Ширина 25%')
                        ->setWidth(Width::W25),
                Text::make('demo_text_width_33', 'Ширина 33%')
                        ->setWidth(Width::W33),
                Text::make('demo_text_width', 'Ширина 50%')
                        ->setWidth(Width::W50),
                Text::make('demo_text_width_75', 'Ширина 75%')
                        ->setWidth(Width::W75),
                Text::make('demo_text_width_full', 'Ширина 100%')
                        ->setWidth(Width::FULL),

                Heading::make('📏 Высота'),
                Text::make('demo_text_height_xs', 'Очень маленький (xs)')
                        ->setHeight(Height::XS),
                Text::make('demo_text_height_sm', 'Маленький (sm)')
                        ->setHeight(Height::SM),
                Text::make('demo_text_height_md', 'Средний (md)')
                        ->setHeight(Height::MD),
                Text::make('demo_text_height_lg', 'Большой (lg)')
                        ->setHeight(Height::LG),

                Heading::make('✏️ Стили текстового поля'),
                Text::make('demo_text_style_round', 'Скруглённый')
                        ->setStyle(TextStyle::ROUND),
                Text::make('demo_text_style_underline', 'Подчёркнутый')
                        ->setStyle(TextStyle::UNDERLINE),
                Text::make('demo_text_style_noborder', 'Без рамки')
                        ->setStyle(TextStyle::NO_BORDER),

                Heading::make('📦 Display'),
                Text::make('demo_text_display', 'Inline (по ширине контента)')
                        ->setDisplay(Display::INLINE),
                Text::make('demo_text_display_block', 'Block (на всю ширину)')
                        ->setDisplay(Display::BLOCK),

                Heading::make('⌨️ Автозаполнение'),
                Text::make('demo_text_autocomplete', 'С автозаполнением')
                        ->setAutocomplete('name')
                        ->setPlaceholder('Браузер предложит сохранённые значения'),

                Heading::make('🔒 Состояния'),
                Text::make('demo_text_disabled', 'Отключён (disabled)')
                        ->setDefault('нельзя изменить')
                        ->setColor(Color::DISABLED)
                        ->disabled(),
                Text::make('demo_text_readonly', 'Только чтение (readonly)')
                        ->setDefault('только для просмотра')
                        ->readonly(),
        ]),

]);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
