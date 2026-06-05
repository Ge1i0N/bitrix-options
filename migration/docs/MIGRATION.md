# Bitrix Options Migrator

> **Package:** `ge1i0n/bitrix-options`
> **Namespace:** `Gelion\BitrixOptionsMigration`

Автоматическое преобразование конфигурационных массивов v1 в код на fluent-интерфейсе v2.

## 1. Обзор

Migrator — это инструмент генерации кода, который преобразует устаревшие конфигурационные массивы v1 в современный PHP-код v2 с fluent-интерфейсом. Он обрабатывает архитектурный переход от простых массивов к типизированным объектам полей.

**Уровень автоматизации: 70-80%.** Большинство типовых конфигураций преобразуются без ручного вмешательства. Сложные конфигурации с нестандартными типами или необычными шаблонами могут потребовать небольших ручных правок, но мигратор поддерживает все 10 встроенных типов полей, сопоставление параметров, трансформацию опций и генерацию импортов.

Общую документацию пакета см. в основном [README.md](../../README.md).

## 2. Быстрый старт

```php
use Gelion\BitrixOptionsMigration\Migrator;

$v1Config = [
    'module_id' => 'my.module.id',
    'tabs' => [
        [
            'DIV' => 'main',
            'TAB' => 'Main Settings',
            'GROUPS' => [
                [
                    'TITLE' => 'General',
                    'OPTIONS' => [
                        'SITE_NAME' => [
                            'TYPE' => 'STRING',
                            'TITLE' => 'Site Name',
                            'DEFAULT' => 'My Site',
                        ],
                        'ENABLE_CACHE' => [
                            'TYPE' => 'CHECKBOX',
                            'TITLE' => 'Enable Cache',
                        ],
                    ],
                ],
            ],
        ],
    ],
];

$migrator = new Migrator();
$result = $migrator->convert($v1Config);

if ($result->success) {
    echo $result->code;
}

if ($result->hasWarnings()) {
    foreach ($result->warnings as $warning) {
        echo $warning['type'] . ': ' . $warning['message'] . PHP_EOL;
    }
}
```

Сохраните результат в PHP-файл и просмотрите предупреждения перед использованием.

## 3. Установка

Мигратор входит в состав пакета `ge1i0n/bitrix-options`. Установка стандартная:

```bash
composer require ge1i0n/bitrix-options --dev
```

### Ручное подключение

Если Composer недоступен, скопируйте каталог `migration/src/` в ваш проект и зарегистрируйте автозагрузчик:

```php
require_once '/path/to/migration/src/Migrator.php';
require_once '/path/to/migration/src/MigrationResult.php';
```

## 4. Инструкция по использованию

### Класс Migrator

```php
use Gelion\BitrixOptionsMigration\Migrator;

$migrator = new Migrator();
$result = $migrator->convert($v1Config);
```

Метод `convert()` принимает массив конфигурации v1 и возвращает value-объект `MigrationResult`.

### Класс MigrationResult

```php
$result->code       // string - сгенерированный PHP-код
$result->warnings   // array  - список предупреждений (каждое содержит type, message, context)
$result->imports    // array  - список use-выражений
$result->success    // bool   - успешность преобразования

$result->hasWarnings()  // bool - true, если есть предупреждения
$result->toArray()      // array - все свойства в виде ассоциативного массива
```

### Возможные ошибки

`convert()` возвращает `success: false` только когда в конфигурации отсутствует `module_id` или `tabs`. Во всех остальных случаях (включая неизвестные типы полей и недопустимые параметры) преобразование выполняется с предупреждениями и `success: true`.

## 5. Входной формат (конфигурация v1)

```php
$v1Config = [
    'module_id' => 'my.module.id',
    'tabs' => [
        [
            'DIV' => 'tab_id',
            'TAB' => 'Tab Title',
            'GROUPS' => [
                [
                    'TITLE' => 'Group Name',
                    'OPTIONS' => [
                        'FIELD_KEY' => [
                            'TYPE' => 'STRING',
                            'TITLE' => 'Field Label',
                            'DEFAULT' => 'value',
                            'NOTES' => 'Help text',
                            'SORT' => 100,
                            // ... other params
                        ],
                    ],
                ],
            ],
        ],
    ],
];
```

Вкладки также могут использовать `OPTIONS` напрямую вместо `GROUPS`:

```php
'tabs' => [
    [
        'DIV' => 'tab_id',
        'TAB' => 'Tab Title',
        'OPTIONS' => [
            'FIELD_KEY' => ['TYPE' => 'STRING', 'TITLE' => 'Field'],
        ],
    ],
],
```

## 6. Выходной формат (сгенерированный код v2)

Для входных данных из раздела 5 мигратор генерирует:

```php
<?php

declare(strict_types=1);

use Gelion\BitrixOptions\Form;
use Gelion\BitrixOptions\Tab;
use Gelion\BitrixOptions\Fields\Heading;
use Gelion\BitrixOptions\Fields\Text;

Form::make('my.module.id', [
    Tab::make('tab_id', 'Tab Title', [
        Heading::make('Group Name'),
        Text::make('FIELD_KEY', 'Field Label')->setDefault('value'),
    ]),
]);
```

Ключевые структурные замечания:

- Импорты группируются: основные классы (Form, Tab), затем классы полей (в алфавитном порядке), затем перечисления (TagColor).
- Группы с заголовком `TITLE` создают вызов `Heading::make()` перед полями группы.
- Группы без `TITLE` не создают заголовок.
- Вкладки без `GROUPS` используют прямой массив `OPTIONS` — поля генерируются непосредственно внутри `Tab::make()`.
- Каждый тип поля сопоставляется с соответствующим классом v2 (см. раздел 7).
- Параметры сопоставляются с цепочками fluent-методов (см. раздел 8).
- Одиночные цепочки методов длиной до 100 символов остаются на одной строке с `make()`.
- Несколько методов или длинные одиночные методы разбиваются на многострочные fluent-цепочки с отступом в 12 пробелов.

## 7. Таблица соответствия типов полей

| v1 TYPE       | v2 Class      | Описание                           |
|---------------|---------------|------------------------------------|
| STRING        | Text          | Обычное текстовое поле             |
| NUMBER        | Number        | Числовое поле с min/max/step       |
| CHECKBOX      | Checkbox      | Переключатель Да/Нет               |
| TEXTAREA      | Textarea      | Многострочный текст                |
| COLORPICKER   | ColorPicker   | Выбор цвета                        |
| DROPDOWN      | Select        | Выпадающий список (один выбор)     |
| MULTISELECT   | Select        | Множественный выбор (+ `->multiple()`) |
| HTMLEDITOR    | HtmlEditor    | Визуальный редактор                |
| CONDITIONS    | Condition     | Конструктор условий                |
| ALERT         | Alert         | Информационное сообщение (без ключа) |

## 8. Таблица соответствия параметров

| v1 Parameter  | v2 Method          | Применяется к                       | Примечания                                    |
|---------------|--------------------|-------------------------------------|-----------------------------------------------|
| TITLE         | make(key, title)   | Все                                 | Второй параметр после ключа                   |
| DEFAULT       | ->setDefault()     | Все                                 | Пустая строка пропускается                    |
| NOTES         | ->setHint()        | Все                                 |                                                |
| PLACEHOLDER   | ->setPlaceholder() | Text, Textarea                      |                                                |
| READONLY      | ->readonly()       | Text, Textarea, Number              | Только если значение истинно                  |
| DISABLED      | ->disabled()       | Большинство полей                   | Только если значение истинно                  |
| AUTOCOMPLETE  | ->setAutocomplete()| Text                                |                                                |
| TAG           | ->setTag()         | Text, Number и др.                  | Комбинируется с TAGCOLOR                      |
| TAGCOLOR      | ->setTag(...)      | Совместно с TAG                     | Преобразуется в перечисление TagColor         |
| STEP          | ->step()            | Только NUMBER                       |                                                |
| MIN           | ->min()            | Только NUMBER                       |                                                |
| MAX           | ->max()            | Только NUMBER                       |                                                |
| COLS          | ->cols()           | Только TEXTAREA                     |                                                |
| ROWS          | ->rows()           | Только TEXTAREA                     |                                                |
| SIZE          | ->size()           | DROPDOWN, MULTISELECT               |                                                |
| OPTIONS       | ->options()        | DROPDOWN, MULTISELECT               | Формат преобразуется (см. Особые случаи)      |
| SORT          | Позиция в массиве  | Все                                 | По умолчанию 500, сортировка по возрастанию, устойчивая |

### Сопоставление TagColor

| v1 TAGCOLOR   | Значение TagColor  |
|---------------|--------------------|
| default       | DEFAULT            |
| primary       | PRIMARY            |
| success       | SUCCESS            |
| danger        | DANGER             |
| warning       | WARNING            |
| info          | INFO (замена на DEFAULT) |

## 9. Особые случаи

### Поле ALERT

Поля ALERT не имеют параметра ключа. Мигратор генерирует `Alert::make('Title')` без аргумента ключа:

```php
// v1 input
'ALERT_MSG' => ['TYPE' => 'ALERT', 'TITLE' => 'Notice text']

// generated output
Alert::make('Notice text'),
```

### MULTISELECT

MULTISELECT сопоставляется с классом `Select` с добавлением `->multiple()`. Вызов `->multiple()` является частью базового вызова `make()`, а не отдельным звеном цепочки:

```php
// generated output
Select::make('COLORS', 'Colors')->multiple(),

// with options
Select::make('COLORS', 'Colors')
    ->multiple()
    ->options(['red' => 'Red', 'blue' => 'Blue']),
```

### GROUPS (Группы)

Группы с заголовком `TITLE` генерируют вызов `Heading::make()` перед своими полями. Группы без `TITLE` не создают заголовок. Пустые группы (без `OPTIONS`) полностью пропускаются, включая их заголовок.

```php
// v1 with title
['TITLE' => 'Group Name', 'OPTIONS' => [...]]

// generated
Heading::make('Group Name'),
Text::make('KEY', 'Title'),

// v1 without title
['OPTIONS' => [...]]

// generated (no heading)
Text::make('KEY', 'Title'),
```

### Сортировка SORT

Поля сортируются по значению `SORT` (по умолчанию 500) по возрастанию. Поля с одинаковым значением SORT сохраняют исходный порядок в массиве (устойчивая сортировка).

### Трансформация опций

В v1 опции представляют собой массив объектов `{TITLE, VALUE}`. Мигратор преобразует их в ассоциативный массив формата v2 `{value => title}`:

```php
// v1 input
'OPTIONS' => [
    ['TITLE' => 'Red', 'VALUE' => 'red'],
    ['TITLE' => 'Green', 'VALUE' => 'green'],
]

// generated
->options(['red' => 'Red', 'green' => 'Green'])
```

Если у элемента опции отсутствует `TITLE`, его `VALUE` используется и как ключ, и как метка. Если у элемента отсутствует `VALUE`, генерируется предупреждение, и элемент пропускается.

### Неизвестные типы

Если тип поля `TYPE` отсутствует в таблице соответствия, мигратор генерирует комментарий-заглушку и добавляет предупреждение:

```php
// generated for unknown type
// CUSTOM_FIELD: Unknown type 'CUSTOM_FIELD' - requires manual migration
```

### Неизвестные параметры

Параметры, отсутствующие в таблице соответствия (и не являющиеся структурными полями вроде TYPE, TITLE, SORT, TAGCOLOR), генерируют предупреждение, но не влияют на выходной код.

### Проверка типовых параметров

Типоспецифичные параметры (STEP, MIN, MAX для NUMBER; COLS, ROWS для TEXTAREA; SIZE, OPTIONS для SELECT) генерируют предупреждение `parameter_type_mismatch`, если используются с несовместимым типом поля. Параметр при этом всё равно включается в вывод.

### Генерация импортов

Импорты генерируются на основе фактического использования:

- **Всегда включены**: Form, Tab
- **Условно**: Heading (группы с TITLE), классы полей (по типу поля), TagColor (поля с параметром TAG)
- **Группировка**: Сначала основные классы, затем классы полей (в алфавитном порядке), затем перечисления

## 10. Справочник предупреждений

| Тип предупреждения          | Описание                                                              |
|-----------------------------|-----------------------------------------------------------------------|
| missing_module_id           | В конфигурации нет `module_id` или это не строка. Преобразование не удалось. |
| missing_tabs                | В конфигурации нет массива `tabs`. Преобразование не удалось.          |
| unknown_field_type          | Тип поля не найден в TYPE_MAP. Генерируется комментарий-заглушка.      |
| unknown_parameter           | Параметр поля не распознан. Пропускается в выводе.                    |
| parameter_type_mismatch     | Типоспецифичный параметр использован с несовместимым типом поля. Всё равно добавляется. |
| invalid_options_format      | У элемента опций отсутствует VALUE. Элемент пропускается.              |

Каждое предупреждение представляет собой ассоциативный массив:

```php
[
    'type' => 'unknown_field_type',
    'message' => "Unknown field type: CUSTOM_TYPE",
    'context' => [
        'key' => 'MY_FIELD',
        'type' => 'CUSTOM_TYPE',
    ],
]
```

## 11. Ограничения

- **Пользовательские типы полей**: Типы, зарегистрированные через `Form::typesRegister()`, не могут быть автоматически преобразованы. Мигратор знает только 10 встроенных типов.
- **Параметры отображения v1**: Параметры типа WIDTH, HEIGHT, COLOR не имеют соответствий в v2. Их может потребоваться добавить вручную.
- **Сложная условная логика**: Условия зависимостей полей из v1 могут потребовать ручной доработки после миграции.
- **Отображаемые значения перечислений v1**: Конфигурации отображаемых значений из перечислений v1 не конвертируются автоматически.
- **Опции в нестандартном формате**: Массивы опций, отличные от шаблона `[{TITLE, VALUE}]`, могут преобразовываться некорректно.
- **Комментарии и пробелы**: Мигратор генерирует чистый код, но не сохраняет комментарии из исходной конфигурации v1.

## 12. Контрольный список после миграции

После запуска мигратора проверьте следующее:

1. **Проверьте предупреждения** — Устраните все предупреждения перед использованием сгенерированного кода.
2. **Проверьте поля ALERT** — Убедитесь, что текст заголовка корректен (у ALERT нет ключа для идентификации).
3. **Проверьте use-выражения** — Убедитесь, что все необходимые импорты присутствуют и нет лишних.
4. **Протестируйте код синтаксически** — Запустите `php -l` для выходного файла, чтобы проверить синтаксис.
5. **Протестируйте в вашем модуле** — Замените конфигурацию v1 сгенерированным кодом и убедитесь, что страница настроек отображается корректно.
6. **Отформатируйте код** — Добавьте или уберите переносы строк, отрегулируйте отступы в соответствии со стилем вашего проекта.
7. **Проверьте порядок HEADING** — Убедитесь, что вызовы `Heading::make()` находятся на правильных позициях относительно своих групп.
8. **Проверьте опции SELECT** — Подтвердите, что значения и метки опций сопоставлены верно, особенно для полей MULTISELECT.

## 13. Примеры

### Пример 1: Простая конфигурация с одной вкладкой

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Gelion\BitrixOptionsMigration\Migrator;

$v1Config = [
    'module_id' => 'demo.settings',
    'tabs' => [
        [
            'DIV' => 'main',
            'TAB' => 'Main Settings',
            'GROUPS' => [
                [
                    'TITLE' => 'Basic',
                    'OPTIONS' => [
                        'SITE_NAME' => [
                            'TYPE' => 'STRING',
                            'TITLE' => 'Site Name',
                            'DEFAULT' => 'My Site',
                        ],
                        'SHOW_LOGO' => [
                            'TYPE' => 'CHECKBOX',
                            'TITLE' => 'Show Logo',
                        ],
                    ],
                ],
            ],
        ],
    ],
];

$migrator = new Migrator();
$result = $migrator->convert($v1Config);
```

Сгенерированный вывод:

```php
<?php

declare(strict_types=1);

use Gelion\BitrixOptions\Form;
use Gelion\BitrixOptions\Tab;
use Gelion\BitrixOptions\Fields\Checkbox;
use Gelion\BitrixOptions\Fields\Heading;
use Gelion\BitrixOptions\Fields\Text;

Form::make('demo.settings', [
    Tab::make('main', 'Main Settings', [
        Heading::make('Basic'),
        Text::make('SITE_NAME', 'Site Name')->setDefault('My Site'),
        Checkbox::make('SHOW_LOGO', 'Show Logo'),
    ]),
]);
```

### Пример 2: Несколько вкладок с группами

```php
$v1Config = [
    'module_id' => 'shop.config',
    'tabs' => [
        [
            'DIV' => 'general',
            'TAB' => 'General',
            'GROUPS' => [
                [
                    'TITLE' => 'Store Info',
                    'OPTIONS' => [
                        'STORE_NAME' => ['TYPE' => 'STRING', 'TITLE' => 'Store Name'],
                        'STORE_EMAIL' => ['TYPE' => 'STRING', 'TITLE' => 'Email'],
                    ],
                ],
                [
                    'TITLE' => 'Display',
                    'OPTIONS' => [
                        'ITEMS_PER_PAGE' => [
                            'TYPE' => 'NUMBER',
                            'TITLE' => 'Items Per Page',
                            'DEFAULT' => '20',
                            'MIN' => 1,
                            'MAX' => 100,
                        ],
                    ],
                ],
            ],
        ],
        [
            'DIV' => 'advanced',
            'TAB' => 'Advanced',
            'OPTIONS' => [
                'DEBUG_MODE' => ['TYPE' => 'CHECKBOX', 'TITLE' => 'Debug Mode'],
                'ALERT_MSG' => ['TYPE' => 'ALERT', 'TITLE' => 'Changes may affect performance'],
            ],
        ],
    ],
];
```

Сгенерированный вывод:

```php
<?php

declare(strict_types=1);

use Gelion\BitrixOptions\Form;
use Gelion\BitrixOptions\Tab;
use Gelion\BitrixOptions\Fields\Alert;
use Gelion\BitrixOptions\Fields\Checkbox;
use Gelion\BitrixOptions\Fields\Heading;
use Gelion\BitrixOptions\Fields\Number;
use Gelion\BitrixOptions\Fields\Text;

Form::make('shop.config', [
    Tab::make('general', 'General', [
        Heading::make('Store Info'),
        Text::make('STORE_NAME', 'Store Name'),
        Text::make('STORE_EMAIL', 'Email'),
        Heading::make('Display'),
        Number::make('ITEMS_PER_PAGE', 'Items Per Page')
            ->setDefault('20')
            ->min(1)
            ->max(100),
    ]),
    Tab::make('advanced', 'Advanced', [
        Checkbox::make('DEBUG_MODE', 'Debug Mode'),
        Alert::make('Changes may affect performance'),
    ]),
]);
```

### Пример 3: Все типы полей

```php
$v1Config = [
    'module_id' => 'demo.alltypes',
    'tabs' => [
        [
            'DIV' => 'fields',
            'TAB' => 'All Fields',
            'OPTIONS' => [
                'TEXT_FIELD' => ['TYPE' => 'STRING', 'TITLE' => 'Text'],
                'NUM_FIELD' => ['TYPE' => 'NUMBER', 'TITLE' => 'Number'],
                'CHK_FIELD' => ['TYPE' => 'CHECKBOX', 'TITLE' => 'Checkbox'],
                'TAR_FIELD' => ['TYPE' => 'TEXTAREA', 'TITLE' => 'Textarea'],
                'CLR_FIELD' => ['TYPE' => 'COLORPICKER', 'TITLE' => 'Color'],
                'DRP_FIELD' => ['TYPE' => 'DROPDOWN', 'TITLE' => 'Dropdown'],
                'MUL_FIELD' => ['TYPE' => 'MULTISELECT', 'TITLE' => 'Multi'],
                'HTML_FIELD' => ['TYPE' => 'HTMLEDITOR', 'TITLE' => 'Html Editor'],
                'CON_FIELD' => ['TYPE' => 'CONDITIONS', 'TITLE' => 'Condition'],
                'ALERT_MSG' => ['TYPE' => 'ALERT', 'TITLE' => 'Info alert'],
            ],
        ],
    ],
];
```

Сгенерированный вывод:

```php
<?php

declare(strict_types=1);

use Gelion\BitrixOptions\Form;
use Gelion\BitrixOptions\Tab;
use Gelion\BitrixOptions\Fields\Alert;
use Gelion\BitrixOptions\Fields\Checkbox;
use Gelion\BitrixOptions\Fields\ColorPicker;
use Gelion\BitrixOptions\Fields\Condition;
use Gelion\BitrixOptions\Fields\HtmlEditor;
use Gelion\BitrixOptions\Fields\Number;
use Gelion\BitrixOptions\Fields\Select;
use Gelion\BitrixOptions\Fields\Text;
use Gelion\BitrixOptions\Fields\Textarea;

Form::make('demo.alltypes', [
    Tab::make('fields', 'All Fields', [
        Text::make('TEXT_FIELD', 'Text'),
        Number::make('NUM_FIELD', 'Number'),
        Checkbox::make('CHK_FIELD', 'Checkbox'),
        Textarea::make('TAR_FIELD', 'Textarea'),
        ColorPicker::make('CLR_FIELD', 'Color'),
        Select::make('DRP_FIELD', 'Dropdown'),
        Select::make('MUL_FIELD', 'Multi')->multiple(),
        HtmlEditor::make('HTML_FIELD', 'Html Editor'),
        Condition::make('CON_FIELD', 'Condition'),
        Alert::make('Info alert'),
    ]),
]);
```

### Пример 4: Сложные комбинации параметров

```php
$v1Config = [
    'module_id' => 'demo.complex',
    'tabs' => [
        [
            'DIV' => 'main',
            'TAB' => 'Main',
            'GROUPS' => [
                [
                    'TITLE' => 'Configuration',
                    'OPTIONS' => [
                        'SITE_NAME' => [
                            'TYPE' => 'STRING',
                            'TITLE' => 'Site Name',
                            'DEFAULT' => 'My App',
                            'NOTES' => 'Shown in browser title',
                            'PLACEHOLDER' => 'Enter site name',
                            'AUTOCOMPLETE' => 'off',
                            'TAG' => 'required',
                            'TAGCOLOR' => 'danger',
                            'SORT' => 100,
                        ],
                        'MAX_ITEMS' => [
                            'TYPE' => 'NUMBER',
                            'TITLE' => 'Max Items',
                            'DEFAULT' => '50',
                            'STEP' => 5,
                            'MIN' => 0,
                            'MAX' => 500,
                            'SORT' => 200,
                        ],
                        'DESCRIPTION' => [
                            'TYPE' => 'TEXTAREA',
                            'TITLE' => 'Description',
                            'COLS' => 80,
                            'ROWS' => 6,
                            'SORT' => 300,
                        ],
                        'THEME' => [
                            'TYPE' => 'DROPDOWN',
                            'TITLE' => 'Theme',
                            'OPTIONS' => [
                                ['TITLE' => 'Light', 'VALUE' => 'light'],
                                ['TITLE' => 'Dark', 'VALUE' => 'dark'],
                                ['TITLE' => 'Auto', 'VALUE' => 'auto'],
                            ],
                            'SORT' => 400,
                        ],
                        'COLORS' => [
                            'TYPE' => 'MULTISELECT',
                            'TITLE' => 'Colors',
                            'SIZE' => 4,
                            'OPTIONS' => [
                                ['TITLE' => 'Red', 'VALUE' => 'red'],
                                ['TITLE' => 'Green', 'VALUE' => 'green'],
                                ['TITLE' => 'Blue', 'VALUE' => 'blue'],
                            ],
                            'SORT' => 500,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
```

Сгенерированный вывод:

```php
<?php

declare(strict_types=1);

use Gelion\BitrixOptions\Form;
use Gelion\BitrixOptions\Tab;
use Gelion\BitrixOptions\Enums\TagColor;
use Gelion\BitrixOptions\Fields\Heading;
use Gelion\BitrixOptions\Fields\Number;
use Gelion\BitrixOptions\Fields\Select;
use Gelion\BitrixOptions\Fields\Text;
use Gelion\BitrixOptions\Fields\Textarea;

Form::make('demo.complex', [
    Tab::make('main', 'Main', [
        Heading::make('Configuration'),
        Text::make('SITE_NAME', 'Site Name')
            ->setDefault('My App')
            ->setHint('Shown in browser title')
            ->setPlaceholder('Enter site name')
            ->setAutocomplete('off')
            ->setTag('required', \Gelion\BitrixOptions\Enums\TagColor::DANGER),
        Number::make('MAX_ITEMS', 'Max Items')
            ->setDefault('50')
            ->step(5)
            ->min(0)
            ->max(500),
        Textarea::make('DESCRIPTION', 'Description')
            ->cols(80)
            ->rows(6),
        Select::make('THEME', 'Theme')
            ->options(['light' => 'Light', 'dark' => 'Dark', 'auto' => 'Auto']),
        Select::make('COLORS', 'Colors')
            ->multiple()
            ->size(4)
            ->options(['red' => 'Red', 'green' => 'Green', 'blue' => 'Blue']),
    ]),
]);
```

---

Для получения дополнительной информации о типах полей v2 и их доступных методах см. основной [README.md](../../README.md).
