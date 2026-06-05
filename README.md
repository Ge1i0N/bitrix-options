[![Latest Stable Version](https://poser.pugx.org/ge1i0n/bitrix-options/v/stable.svg)](https://packagist.org/packages/ge1i0n/bitrix-options/)
[![Total Downloads](https://img.shields.io/packagist/dt/ge1i0n/bitrix-options.svg?style=flat)](https://packagist.org/packages/ge1i0n/bitrix-options)

# Bitrix Options — генератор форм настроек в административной панели Битрикс

Пакет для удобной генерации страниц настроек модулей в административной панели 1С-Битрикс. Позволяет описывать форму
настроек в декларативном стиле через fluent-интерфейс — без шаблонов и без ручного HTML.

---

## Содержание

- [Поддержка версий](#поддержка-версий)
- [Установка](#установка)
- [Быстрый старт](#быстрый-старт)
- [Демо-пример](#демо-пример-examplesdemophp)
- [Схема работы](#схема-работы)
- [Типы полей](#типы-полей)
- [Общие трейты (fluent-методы)](#общие-трейты-fluent-методы)
- [Безопасность](#безопасность)
- [Тестирование](#тестирование)
- [Статический анализ](#статический-анализ)
- [Структура пакета](#структура-пакета)
- [Дополнительно](#дополнительно)

---

## Поддержка версий

| BitrixOptions | mpm.options | PHP  |
|---------------|-------------|------|
| 1.0+          | 1.0+        | 7.0+ |
| 1.0+, 2.0+    | 2.0+        | 8.2+ |

---

## Установка

### Через marketplace

1. Установить [модуль mpm.options](http://marketplace.1c-bitrix.ru/solutions/mpm.options/) из маркетплейса Битрикс.
2. Создать файл настроек модуля (например, `options.php`).
3. Подключить модуль и вызвать генератор формы:

```php
Bitrix\Main\Loader::includeModule('mpm.options');

Form::make('youmodule.id', [
    Tab::make('first', 'Первая вкладка', []),
    Tab::make('second', 'Вторая вкладка', []),
]);
```

### Через Composer

```bash
composer require ge1i0n/bitrix-options
```

```php
use Gelion\BitrixOptions\Form;
use Gelion\BitrixOptions\Tab;
use Gelion\BitrixOptions\Fields\Text;
use Gelion\BitrixOptions\Fields\Number;

Form::make('youmodule.id', [
    Tab::make('first', 'Первая вкладка', [
        Text::make('TEXT'),
    ]),
    Tab::make('second', 'Вторая вкладка', [
        Number::make('NUMBER'),
    ]),
]);
```

### Подключение в свой модуль для маркетплейса (без Composer)

Если ваш модуль распространяется через marketplace и не использует Composer, скопируйте файлы пакета в свой модуль и
зарегистрируйте автозагрузку в `include.php`:

```php
Bitrix\Main\Loader::registerAutoLoadClasses('youmodule.id', [
    'Gelion\\BitrixOptions\\Tab'                     => '/lib/Tab.php',
    'Gelion\\BitrixOptions\\Form'                    => '/lib/Form.php',
    'Gelion\\BitrixOptions\\Enums\\Color'             => '/lib/Enums/Color.php',
    'Gelion\\BitrixOptions\\Enums\\Width'             => '/lib/Enums/Width.php',
    'Gelion\\BitrixOptions\\Enums\\Height'            => '/lib/Enums/Height.php',
    'Gelion\\BitrixOptions\\Enums\\Resize'            => '/lib/Enums/Resize.php',
    'Gelion\\BitrixOptions\\Enums\\Display'           => '/lib/Enums/Display.php',
    'Gelion\\BitrixOptions\\Enums\\TagColor'          => '/lib/Enums/TagColor.php',
    'Gelion\\BitrixOptions\\Enums\\AlertIcon'         => '/lib/Enums/AlertIcon.php',
    'Gelion\\BitrixOptions\\Enums\\AlertSize'         => '/lib/Enums/AlertSize.php',
    'Gelion\\BitrixOptions\\Enums\\TextStyle'         => '/lib/Enums/TextStyle.php',
    'Gelion\\BitrixOptions\\Enums\\AlertTextPosition' => '/lib/Enums/AlertTextPosition.php',
    'Gelion\\BitrixOptions\\Fields\\Date'             => '/lib/Fields/Date.php',
    'Gelion\\BitrixOptions\\Fields\\Text'             => '/lib/Fields/Text.php',
    'Gelion\\BitrixOptions\\Fields\\Alert'            => '/lib/Fields/Alert.php',
    'Gelion\\BitrixOptions\\Fields\\Email'            => '/lib/Fields/Email.php',
    'Gelion\\BitrixOptions\\Fields\\Number'           => '/lib/Fields/Number.php',
    'Gelion\\BitrixOptions\\Fields\\Select'           => '/lib/Fields/Select.php',
    'Gelion\\BitrixOptions\\Fields\\Heading'          => '/lib/Fields/Heading.php',
    'Gelion\\BitrixOptions\\Fields\\Checkbox'         => '/lib/Fields/Checkbox.php',
    'Gelion\\BitrixOptions\\Fields\\Textarea'         => '/lib/Fields/Textarea.php',
    'Gelion\\BitrixOptions\\Fields\\Condition'        => '/lib/Fields/Condition.php',
    'Gelion\\BitrixOptions\\Fields\\HtmlEditor'       => '/lib/Fields/HtmlEditor.php',
    'Gelion\\BitrixOptions\\Fields\\ColorPicker'      => '/lib/Fields/ColorPicker.php',
    'Gelion\\BitrixOptions\\Traits\\WithTag'          => '/lib/Traits/WithTag.php',
    'Gelion\\BitrixOptions\\Traits\\WithHint'         => '/lib/Traits/WithHint.php',
    'Gelion\\BitrixOptions\\Traits\\WithColor'        => '/lib/Traits/WithColor.php',
    'Gelion\\BitrixOptions\\Traits\\WithModuleId'     => '/lib/Traits/WithModuleId.php',
    'Gelion\\BitrixOptions\\Traits\\WithStyleClass'   => '/lib/Traits/WithStyleClass.php',
    'Gelion\\BitrixOptions\\Traits\\WithDisplayWithoutValue' => '/lib/Traits/WithDisplayWithoutValue.php',
]);
```

---

## Быстрый старт

```php
use Gelion\BitrixOptions\Form;
use Gelion\BitrixOptions\Tab;
use Gelion\BitrixOptions\Fields\Text;
use Gelion\BitrixOptions\Fields\Checkbox;
use Gelion\BitrixOptions\Fields\Select;

Form::make('my.module.id', [
    Tab::make('main', 'Основные', [
        Text::make('SITE_NAME', 'Название сайта')
            ->setDefault('Мой сайт')
            ->setHint('Отображается в заголовке'),
        Checkbox::make('ENABLE_CACHE', 'Включить кеширование'),
        Select::make('THEME', 'Тема оформления')
            ->options([
                'light' => 'Светлая',
                'dark'  => 'Тёмная',
            ]),
    ]),
]);
```

Поместите этот код в `options.php` вашего модуля. Файл должен быть доступен из административной панели через меню
«Настройки» → «Настройки продукта» → «Настройки модулей».

---

## Демо-пример (`examples/demo.php`)

В пакете есть полноценный демонстрационный файл `examples/demo.php`, который показывает **все типы полей** пакета
с различными комбинациями настроек. Это отличная отправная точка для изучения возможностей пакета.

### Что показывает демо

Демо-страница содержит три вкладки:

| Вкладка | Что показывает |
|---------|----------------|
| **📋 Базовые поля** | Text, Textarea (с resize, без resize, количество строк), Number (с min/max/step), Checkbox, Select (одиночный и множественный) |
| **⚙️ Дополнительные поля** | ColorPicker, Date, Email, Heading, Alert (все варианты icon/size/textPosition/color), HtmlEditor, Condition |
| **🎨 Стилизация** | Примеры всех fluent-модификаторов: hint, tag (разные TagColor), color обводки (Color), ширина (Width), высота (Height), стиль (TextStyle), display, autocomplete, disabled/readonly |

### Как запустить демо

1. Скопируйте `examples/demo.php` в публичную директорию вашего проекта (например, `local/admin/` или `bitrix/admin/`).
2. Откройте страницу в браузере: `http://your-project.local/bitrix/admin/demo.php`.
3. Сохраняйте/изменяйте значения — они хранятся в `Bitrix\Main\Config\Option` для модуля `bitrix_options_demo`.
4. Нажмите «Сбросить демо-опции» чтобы очистить все сохранённые значения.

> **Важно:** Демо-файл предназначен **только для локальной разработки**. Не используйте его на боевых сайтах.

---

## Схема работы

На странице настроек вызывается статический метод `Form::make()`, в который передаются:

1. **ID модуля** — строка, идентификатор вашего модуля.
2. **Массив табов** — описание вкладок и полей внутри них.

```php
Form::make('youmodule.id', [
    Tab::make('tab_id', 'Название вкладки', [
        // Поля...
    ]),
]);
```

Каждый Tab содержит массив полей. Порядок полей соответствует порядку в массиве (в отличие от v1.x, где использовалось
поле сортировки).

---

## Типы полей

### Общая информация

Все типы полей создаются через статический метод `make()`, который принимает:

- **Ключ в БД** — название опции в `Bitrix\Main\Config\Option`.
- **Пояснение (опционально)** — строка, отображаемая рядом с полем. Если передан только один параметр, он используется
  как пояснение, а ключ не используется (для полей, которые ничего не хранят).

Fluent-методы возвращают `$this`, поэтому их можно вызывать цепочкой.

---

### `Heading` — Заголовок

Выводит заголовок-разделитель между группами полей. Заменяет группы полей из v1.x.

```php
Heading::make('Я заголовок группы');

// С поддержкой HTML
Heading::make('Настройки <b>импорта</b>')->allowHtml();
```

По умолчанию HTML экранируется. `->allowHtml()` отключает экранирование — используйте с осторожностью.

---

### `Alert` — Предупреждение / Информационное сообщение

Выводит блок с сообщением, используя [ui.alerts из D7](https://dev.1c-bitrix.ru/api_d7/bitrix/ui/messages/alerts.php).
Настраивается через fluent-методы:

```php
Alert::make('Я предупреждение')
    ->setIcon(AlertIcon::INFO)
    ->setSize(AlertSize::XS)
    ->setTextPosition(AlertTextPosition::CENTER);

// С поддержкой HTML (ссылки, форматирование)
Alert::make('Подробнее в <a href="/docs">документации</a>')
    ->setIcon(AlertIcon::INFO)
    ->allowHtml();
```

**Enums для Alert:**

| Enum | Значения |
|------|----------|
| `AlertIcon` | `INFO`, `WARNING`, `DANGER` |
| `AlertSize` | `XS`, `SM`, `MD`, `LG` |
| `AlertTextPosition` | `CENTER`, `INLINE`, `BLOCK` |
| `Color` | `ACTIVE`, `SUCCESS`, `WARNING`, `DANGER`, `DISABLED` |

---

### `Text` — Текстовое поле

Базовое поле, от которого наследуются все остальные (Date, Email, Number, Checkbox и др.).

```php
Text::make('TEXT_OPTION', 'Я текстовое поле')
    ->disabled(true)
    ->readonly(true)
    ->setDefault('default')
    ->setAutocomplete('username')
    ->setHint('Я строка подсказки')
    ->setPlaceholder('Я строка плейсхолдер')
    ->setStyle(TextStyle::ROUND)
    ->setDisplay(Display::BLOCK)
    ->setTag('Я строка тега', TagColor::SUCCESS)
    ->setHeight(Height::MD)
    ->setWidth(Width::W75)
    ->setColor(Color::SUCCESS);
```

---

### `Date` — Поле для даты

Текстовое поле с типом `date`.

```php
Date::make('DATE_OPTION', 'Я поле даты');
```

---

### `Email` — Поле для email

Текстовое поле с типом `email`.

```php
Email::make('EMAIL_OPTION', 'Я поле электронной почты');
```

---

### `Number` — Числовое поле

Текстовое поле с типом `number`. Дополнительные методы: `min()`, `max()`, `step()`.

```php
Number::make('NUMBER_OPTION', 'Я поле ввода чисел')
    ->min(0)
    ->max(100)
    ->step(2);
```

---

### `Checkbox` — Переключатель

Хранит значение в формате `Y` / `N`, отображается как чекбокс.

```php
Checkbox::make('CHECKBOX_OPTION', 'Я поле ввода чекбокса');
```

---

### `ColorPicker` — Выбор цвета

Поле выбора цвета. Дополнительные методы: `cols()`, `rows()`.

```php
ColorPicker::make('COLOR_OPTION', 'Выберите цвет');
```

> **Важно:** При сохранении страницы, если цвет не выбран, по умолчанию сохраняется чёрный.

---

### `Textarea` — Многострочное поле

Поле для многострочного ввода.

```php
Textarea::make('TEXTAREA_OPTION', 'Я поле многострочного ввода')
    ->setResize(Resize::RESIZE_X)
    ->cols(10)
    ->rows(10);
```

**Enums для Resize:** `NO_RESIZE`, `RESIZE_X`, `RESIZE_Y`, `RESIZE_BOTH`.

---

### `Select` — Выбор из списка

Селектор с поддержкой одиночного/множественного выбора.

```php
Select::make('SELECT_OPTION', 'Я поле выбора из списка')
    ->options([
        'key-1' => 'Вариант 1',
        'key-2' => 'Вариант 2',
    ])
    ->size(5)        // Атрибут size
    ->multiple();    // Включить множественный выбор
```

При `multiple()` имя поля в форме становится `KEY[]`, а значение в БД хранится в сериализованном виде.

---

### `HtmlEditor` — HTML-редактор

Редактор с переключением между режимами text, html и визуальный редактор.

```php
HtmlEditor::make('HTML_EDITOR_OPTION', 'Я поле редактора')
    ->setHint('Я строка подсказки');
```

---

### `Condition` — Конструктор условий

Поле выбора условий, основанное на классе `CCatalogCondTree`. Используется для построения сложных логических условий
(например, правил работы с корзиной). Значение хранится в БД в формате `base64(serialize(...))`.

```php
Condition::make('CONDITION_OPTION', 'Я поле условия');
```

---

## Общие трейты (fluent-методы)

Пакет использует трейты для переиспользования fluent-методов между полями. Понимание трейтов помогает понять, какие
методы доступны у каждого поля.

| Трейт | Методы | Описание | Применяется в |
|-------|--------|----------|---------------|
| `WithModuleId` | `setModuleId()` | Устанавливает ID модуля (вызывается автоматически из `Tab::view()`) | Все поля |
| `WithHint` | `setHint()` | Добавляет всплывающую подсказку с иконкой | Text, Textarea, Number, Checkbox, HtmlEditor и др. |
| `WithTag` | `setTag(string, TagColor)` | Добавляет цветной тег рядом с полем | Text, Number, Email, Date, Checkbox, ColorPicker |
| `WithColor` | `setColor(Color)` | Устанавливает цвет обводки поля | Text, Number, Email, Date, Checkbox, ColorPicker, Alert |
| `WithStyleClass` | `getClassFromProps()` | Собирает CSS-классы из свойств (display, width, height, color, style) | Text, Alert |
| `WithDisplayWithoutValue` | — | Для полей, которые отображают информацию, но не хранят значения | Alert, Heading |

### Enum-классы

| Enum | Значения | Для чего |
|------|----------|----------|
| `Color` | `ACTIVE`, `SUCCESS`, `WARNING`, `DANGER`, `DISABLED`, `HOVER` | Цвет обводки |
| `Width` | `DEFAULT`, `AUTO`, `W25`, `W33`, `W50`, `W75`, `FULL` | Ширина поля |
| `Height` | `XS`, `SM`, `MD`, `LG` | Высота поля |
| `TextStyle` | `ROUND`, `UNDERLINE`, `NO_BORDER` | Стиль текстового поля |
| `Display` | `INLINE`, `BLOCK` | Режим отображения |
| `TagColor` | `PRIMARY`, `SUCCESS`, `DANGER`, `WARNING`, `INFO` | Цвет тега |
| `Resize` | `NO_RESIZE`, `RESIZE_X`, `RESIZE_Y`, `RESIZE_BOTH` | Направление ресайза Textarea |

---

## Безопасность

### Экранирование данных

Все данные, выводимые в HTML-атрибуты, экранируются через `htmlspecialchars()`. Это защищает от XSS-атак при вводе
специальных символов в названия, значения и другие текстовые поля.

```php
// Значение <script>alert(1)</script> будет экранировано
Text::make('KEY', 'Название');
```

### `allowHtml()` — осознанный риск

Метод `allowHtml()` доступен для полей `Alert` и `Heading`. Он отключает экранирование, позволяя выводить HTML-разметку.

**Используйте `allowHtml()` только когда:**
- Вы доверяете источнику данных (статическая строка в коде, а не пользовательский ввод).
- Вам действительно нужен HTML внутри поля (ссылки, жирный текст, списки).

Если вы передаёте пользовательские данные в поле с `allowHtml()`, убедитесь, что они экранированы самостоятельно.

### Сериализация

Поля `Select` (множественный), `HtmlEditor` и `Condition` хранят данные в сериализованном виде. Пакет корректно
обрабатывает краевые случаи: `false`, пустую строку, некорректный base64.

---

## Тестирование

Пакет имеет 177 PHPUnit-тестов, которые покрывают:

- **Form** — рендеринг формы, экранирование moduleId, кнопки, заголовки.
- **Tab** — создание вкладок, рендеринг контента.
- **Fields** — все 12 типов полей: создание, fluent-интерфейс, HTML на выходе, заполнение значений.
- **Security** — XSS во всех полях, обработка unserialize краевых случаев.
- **Traits** — 6 трейтов: корректная установка свойств.

### Запуск тестов

```bash
# Все тесты
./vendor/bin/phpunit

# С детальным выводом имён тестов
./vendor/bin/phpunit --testdox

# Только тесты безопасности
./vendor/bin/phpunit tests/Security/

# Только тесты полей
./vendor/bin/phpunit tests/Fields/

# Только тесты одного поля (например, Text)
./vendor/bin/phpunit tests/Fields/TextTest.php

# Только тесты одного метода (например, тест XSS в значениях)
./vendor/bin/phpunit --filter test_text_escapes_xss_in_value tests/

# С покрытием кода (требуется Xdebug или PCOV)
./vendor/bin/phpunit --coverage-html coverage/
```

### Структура тестов

```
tests/
├── bootstrap.php           # Автозагрузка + подключение моков
├── FormTest.php            # Тесты Form (рендеринг, экранирование, кнопки)
├── TabTest.php             # Тесты Tab (создание, рендеринг, пустые поля)
├── Enums/                  # Тесты всех Enum-классов (10 файлов)
│   ├── AlertIconTest.php
│   ├── ColorTest.php
│   └── ...
├── Fields/                 # Тесты всех типов полей (12 файлов)
│   ├── TextTest.php
│   ├── SelectTest.php
│   ├── AlertTest.php
│   └── ...
├── Security/               # Тесты безопасности
│   ├── XssTest.php         #   XSS во всех типах полей
│   └── UnserializeTest.php #   Unserialize краевые случаи
├── Traits/                 # Тесты трейтов (6 файлов)
│   ├── WithHintTest.php
│   ├── WithTagTest.php
│   └── ...
└── Mocks/                  # Заглушки для Bitrix CMS (без БД!)
    ├── BitrixMocks.php     #   check_bitrix_sessid(), bitrix_sessid_post()
    ├── OptionMock.php      #   Bitrix\Main\Config\Option
    ├── ApplicationMock.php #   Bitrix\Main\Application
    ├── CAdminTabControl.php #   CAdminTabControl
    ├── CFileMan.php        #   CFileMan
    ├── FormDependencies.php
    ├── CCatalogCondTree.php #   CCatalogCondTree
    └── PhpstanStubs.php    #   Стабы для PHPStan
```

### Как работают тесты (без реального Bitrix)

Тесты не требуют установленного 1С-Битрикс. Вместо него используются моки в `tests/Mocks/`:

- **`BitrixMocks.php`** — глобальные функции `check_bitrix_sessid()` и `bitrix_sessid_post()`.
- **`OptionMock.php`** — `Bitrix\Main\Config\Option`, который хранит данные в статическом массиве (не в БД).
- **`ApplicationMock.php`** — `Bitrix\Main\Application` и `Bitrix\Main\HttpRequest`.
- **`CAdminTabControl.php`** — заглушка класса `CAdminTabControl` для рендеринга вкладок.

Это позволяет запускать тесты в любой среде, где есть PHP и Composer.

### Как написать свой тест

```php
namespace Gelion\BitrixOptions\Tests\Fields;

use Gelion\BitrixOptions\Fields\Text;
use PHPUnit\Framework\TestCase;

final class MyFieldTest extends TestCase
{
    protected function tearDown(): void
    {
        // Сброс моков после каждого теста
        \Bitrix\Main\Application::reset();
        \Bitrix\Main\Config\Option::reset();
    }

    public function test_field_renders(): void
    {
        $field = Text::make('MY_KEY', 'My Title');
        $html = $field->view();

        $this->assertStringContainsString('MY_KEY', $html);
        $this->assertStringContainsString('My Title', $html);
    }
}
```

### Тестирование с mock-данными

Моки `Option` позволяют проверить чтение/запись значений без базы данных:

```php
// Запись тестового значения
\Bitrix\Main\Config\Option::set('test.module', 'my_key', 'stored_value');

// Чтение значения полем
$field = Text::make('my_key');
$field->setModuleId('test.module');
$field->fillValue();

$this->assertSame('stored_value', $field->value);
```

---

## Статический анализ

Пакет проверяется PHPStan на уровне `--level=9`:

```bash
./vendor/bin/phpstan analyse src --level=9
```

---

## Структура пакета

```
bitrix-options/
├── src/                      # Gelion\BitrixOptions — основной API
│   ├── Form.php              # Генератор формы (точка входа)
│   ├── Tab.php               # Вкладка формы
│   ├── Fields/               # Типы полей (12 шт.)
│   ├── Enums/                # Enum-классы для типизированных параметров
│   └── Traits/               # Fluent-трейты
├── migration/                # Gelion\BitrixOptionsMigration — инструменты миграции
│   ├── src/
│   │   ├── Migrator.php      #   Конвертер v1 → v2
│   │   └── MigrationResult.php #   Результат миграции
│   ├── examples/             # Примеры конфигураций для миграции
│   │   ├── simple.php
│   │   ├── complex.php
│   │   └── all-field-types.php
│   └── docs/
│       └── MIGRATION.md      # Документация по миграции
├── tests/                    # PHPUnit-тесты (177)
│   ├── bootstrap.php
│   ├── FormTest.php
│   ├── TabTest.php
│   ├── Fields/
│   ├── Enums/
│   ├── Security/
│   ├── Traits/
│   └── Mocks/
├── examples/
│   └── demo.php              # Полный демо-пример всех полей
├── composer.json
├── phpunit.xml
├── phpstan.neon
```

---

## Миграция с v1 на v2

Если вы используете старую версию пакета с конфигурацией в виде массивов,
утилита миграции автоматически преобразует v1-конфиг в современный fluent-код v2.

```php
use Gelion\BitrixOptionsMigration\Migrator;

$migrator = new Migrator();
$result = $migrator->convert($v1Config);

if ($result->success) {
    echo $result->code;  // Сгенерированный v2-код
}
```

Подробнее: [migration/docs/MIGRATION.md](migration/docs/MIGRATION.md)
Примеры конфигураций: [migration/examples/](migration/examples/)

---

## Дополнительно

- [Документация по UI Bitrix D7](https://dev.1c-bitrix.ru/api_d7/bitrix/ui/index.php)

---

## Разработка

### Запуск тестов

```bash
composer install
./vendor/bin/phpunit
```

### Статический анализ

```bash
./vendor/bin/phpstan analyse src --level=9
```
