[![Latest Stable Version](https://poser.pugx.org/ge1i0n/bitrix-options/v/stable.svg)](https://packagist.org/packages/ge1i0n/bitrix-options/)
[![Total Downloads](https://img.shields.io/packagist/dt/ge1i0n/bitrix-options.svg?style=flat)](https://packagist.org/packages/ge1i0n/bitrix-options)

# Bitrix Options - генератор форм настроек в административной панели Битрикс

## Установка через маркетплейс
1. Установить [модуль mpm.options](http://marketplace.1c-bitrix.ru/solutions/mpm.options/) из маркетплейса bitrix.
2. Создать файл настроек модуля (options.php или подобный)
3. Подключить модуль и вызвать генератор, передав нужные параметры
    ```php
    Bitrix\Main\Loader::includeModule('mpm.options');
    Gelion\BitrixOptions\Form::generate('youmodule.id', $options);
    ```
## Установка через composer
1. Установить пакет `ge1i0n/bitrix-options`
    ```bash
    composer require ge1i0n/bitrix-options
    ```
2. Создать файл настроек модуля (options.php или подобный)
3. Подключить вызвать генератор, передав нужные параметры
    ```php
    Gelion\BitrixOptions\Form::generate('youmodule.id', $options);
    ```

## Пример установки в свой модуль для маркетплейса
1. Скопировать пакет к себе в модуль
2. Зарегистрировать классы модуля в файле `include.php`, прописав корректные пути к классам.
    ```php
    Bitrix\Main\Loader::registerAutoLoadClasses('youmodule.id', [
        'Gelion\\BitrixOptions\\Form'               => '/lib/Form.php',
        'Gelion\\BitrixOptions\\TypeInterface'      => '/lib/TypeInterface.php',
        'Gelion\\BitrixOptions\\TypeBase'           => '/lib/TypeBase.php',
        'Gelion\\BitrixOptions\\TypeForm'           => '/lib/TypeForm.php',
        'Gelion\\BitrixOptions\\Types\\Alert'       => '/lib/Types/Alert.php',
        'Gelion\\BitrixOptions\\Types\\Checkbox'    => '/lib/Types/Checkbox.php',
        'Gelion\\BitrixOptions\\Types\\Colorpicker' => '/lib/Types/Colorpicker.php',
        'Gelion\\BitrixOptions\\Types\\Conditions'  => '/lib/Types/Conditions.php',
        'Gelion\\BitrixOptions\\Types\\Dropdown'    => '/lib/Types/Dropdown.php',
        'Gelion\\BitrixOptions\\Types\\HtmlEditor'  => '/lib/Types/HtmlEditor.php',
        'Gelion\\BitrixOptions\\Types\\MultiSelect' => '/lib/Types/MultiSelect.php',
        'Gelion\\BitrixOptions\\Types\\Number'      => '/lib/Types/Number.php',
        'Gelion\\BitrixOptions\\Types\\Text'        => '/lib/Types/Text.php',
        'Gelion\\BitrixOptions\\Types\\Textarea'    => '/lib/Types/Textarea.php',
    ]);
    ```

## Структура параметров в генераторе
В генератор передаётся массив табов, групп и опций на странице настроек.

```php
$options = [
    [
        'DIV' => 'settings',
        'TAB' => 'Новая вкладка',
        'TITLE' => 'Новая вкладка',
        'ICON' => '',
        'GROUPS' => [
            'GROUP_CODE' => [
                'TITLE' => 'Название группы',
                'OPTIONS' => []
            ],
        ],
    ],
];
```
В `OPTIONS` внутри группы передаётся массив опций, где в качестве ключа указан ID опции в базе данных.

## Типы опций
### Строка
```php
'PROP_ID' => [
    'SORT' => 100,
    'TYPE' => 'STRING',
    'FIELDS' => [
        'TITLE' => 'Поле "Строка"',
        'DEFAULT' => 'Значение по умолчанию',
        'NOTES' => 'Это подсказка к полю "Строка"',
        'PLACEHOLDER' => 'Это плейсхолдер к полю "Строка"',
        'TAG' => 'Текст на теге',
        'READONLY' => false,
        'DISABLED' => false,
        'AUTOCOMPLETE' => false,
    ],
],
```

### Число
```php
'PROP_ID' => [
    'SORT' => 100,
    'TYPE' => 'NUMBER',
    'FIELDS' => [
        'TITLE' => 'Поле "Число"',
        'DEFAULT' => 42,
        'NOTES' => 'Это подсказка к полю "Число"',
        'PLACEHOLDER' => 'Это плейсхолдер к полю "Число"',
        'TAG' => 'Текст на теге',
        'READONLY' => false,
        'DISABLED' => false,
        'AUTOCOMPLETE' => false,
        'STEP' => 1,
        'MIN' => 0,
        'MAX' => 100,
    ],
],
```

### Текст
```php
'PROP_ID' => [
    'SORT' => 100,
    'TYPE' => 'TEXTAREA',
    'FIELDS' => [
        'TITLE' => 'Поле "Текст"',
        'DEFAULT' => 'Значение по умолчанию',
        'NOTES' => 'Это подсказка к полю "Текст"',
        'PLACEHOLDER' => 'Это плейсхолдер к полю "Текст"',
        'TAG' => 'Текст на теге',
        'READONLY' => false,
        'DISABLED' => false,
        'AUTOCOMPLETE' => false,
        'COLS' => 5,
        'ROWS' => 10,
    ],
],
```

### Чекбокс
```php
'PROP_ID' => [
    'SORT' => 100,
    'TYPE' => 'CHECKBOX',
    'FIELDS' => [
        'TITLE' => 'Поле "Чекбокс"',
        'DEFAULT' => 'Y',
        'NOTES' => 'Это подсказка к полю "Чекбокс"',
    ],
],
```

### Выбор цвета
```php
'PROP_ID' => [
    'SORT' => 100,
    'TYPE' => 'COLORPICKER',
    'FIELDS' => [
        'TITLE' => 'Поле "Выбор цвета"',
        'NOTES' => 'Это подсказка к полю "Выбор цвета"',
        'DEFAULT' => '#FFFFFF',
    ],
],
```

### Выпадающий список
```php
'PROP_ID' => [
    'SORT' => 100,
    'TYPE' => 'DROPDOWN',
    'FIELDS' => [
        'TITLE' => 'Поле "Список"',
        'DEFAULT' => 'val-2',
        'NOTES' => 'Это подсказка к полю "Список"',
        'TAG' => 'Текст на теге',
        'OPTIONS' => [
            [
                'TITLE' => 'Первое свойство',
                'VALUE' => 'val-1',
            ],
            [
                'TITLE' => 'Второе свойство',
                'VALUE' => 'val-2',
            ],
        ],
    ],
],
```

### Множественный список
```php
'PROP_ID' => [
    'SORT' => 100,
    'TYPE' => 'MULTISELECT',
    'FIELDS' => [
        'TITLE' => 'Поле "множественный список"',
        'DEFAULT' => serialize(['val-1','val-2']),
        'NOTES' => 'Это подсказка к полю "множественный список"',
        'TAG' => 'Текст на теге',
        'OPTIONS' => [
            [
                'TITLE' => 'Первое свойство',
                'VALUE' => 'val-1',
            ],
            [
                'TITLE' => 'Второе свойство',
                'VALUE' => 'val-2',
            ],
        ],
    ],
],
```

### HTML-редактор
```php
'PROP_ID' => [
    'SORT' => 100,
    'TYPE' => 'HTMLEDITOR',
    'FIELDS' => [
        'TITLE' => 'Поле "HTML-редактор"',
        'NOTES' => 'Это подсказка к полю "HTML-редактор"',
    ],
    'PARAMS' => [
        'WIDTH' => '100%',
        'HEIGHT' => '500',
        'PHP' => false,
        'TASKBAR' => false,
    ]
],
```

### Условия
```php
'PROP_ID' => [
    'SORT' => 100,
    'TYPE' => 'CONDITIONS',
    'FIELDS' => [
        'TITLE' => 'Поле "Условия"',
        'NOTES' => 'Это подсказка к полю "Условия"',
    ],
],
```

### Сообщения
Эта опция ничего не сохраняет в базу данных, но позволяет выводить информационные сообщения на странице настроек. Опция имеет свои собственные параметры о которых можно прочесть ниже.
```php
'PROP_ID' => [
    'SORT' => 100,
    'TYPE' => 'ALERT',
    'FIELDS' => [
        'TITLE' => 'Поле "Сообщения"',
    ],
],
```

## Модификаторы опций
Для опций категории "Формы" можно передать дополнительные параметры, влияющие на отображение формы на странице. Более подробно о принимаемых параметрах и их значениях можно узнать в [документации Bitrix](https://dev.1c-bitrix.ru/api_d7/bitrix/ui/forms/common.php)
Дополнительно доступные параметры пропасаны в базовом классе.
```php
'PROP_ID' => [
    'FIELDS' => [],
    'PARAMS' => [
        'DISPLAY' => 'block',
        'WIDTH' => 'wd',
        'HEIGHT' => 'md',
        'COLOR' => 'default',
        'TAGCOLOR' => 'default',
        'MODIFICATOR' => false,
    ],
],
```

Параметры по умолчанию для опции "Сообщения". О доступных параметрах  значениях можно узнать в [документации Bitrix](https://dev.1c-bitrix.ru/api_d7/bitrix/ui/messages/alerts.php).
```php
'PROP_ID' => [
    'FIELDS' => [],
    'PARAMS' => [
        'DISPLAY' => false,
        'HEIGHT' => false,
        'COLOR' => 'default',
        'ICON' => false,
    ],
],
```

## Дополнительные типы опций
Чтобы создать свой тип опции нужно создать свой класс, унаследовавшись от класса `TypeBase` и с интерфейсом `TypeInterface`.
Затем, где-нибудь перед вызовом генератора зарегистрировать сопоставление названий типов, и классов, которые должны быть инициализированы.
```php
Gelion\BitrixOptions\Form::typesRegister([
    'CUSTOM_TYPE' => MyCustomOptionType::class,
]);
```
Зарегистрированные типы опций, при совпадении названий будут перебивать стандартные.