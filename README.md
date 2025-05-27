[![Latest Stable Version](https://poser.pugx.org/ge1i0n/bitrix-options/v/stable.svg)](https://packagist.org/packages/ge1i0n/bitrix-options/)
[![Total Downloads](https://img.shields.io/packagist/dt/ge1i0n/bitrix-options.svg?style=flat)](https://packagist.org/packages/ge1i0n/bitrix-options)

# Bitrix Options - генератор форм настроек в административной панели Битрикс

### Поддержка версий

| BitrixOptions | mpm.options | php  |
|---------------|-------------|------|
| 1.0+          | 1.0+        | 7.0+ |
| 1.0+, 2.0+    | 2.0+        | 8.1+ |

### Установка через маркетплейс

1. Установить [модуль mpm.options](http://marketplace.1c-bitrix.ru/solutions/mpm.options/) из маркетплейса bitrix.
2. Создать файл настроек модуля (options.php или подобный)
3. Подключить модуль и вызвать генератор формы, передав нужные параметры
    ```php
    Bitrix\Main\Loader::includeModule('mpm.options');
   
    Form::make('youmodule.id', [
        Tab::make('first', 'Первая вкладка', []),
        Tab::make('second', 'Вторая вкладка', []),
    ]);
   ```

### Установка через composer

1. Установить пакет `ge1i0n/bitrix-options`
    ```bash
    composer require ge1i0n/bitrix-options
    ```
2. Создать файл настроек модуля (options.php или подобный)
3. Подключить вызвать генератор, передав нужные параметры
    ```php
    Gelion\BitrixOptions\Form::make('youmodule.id', [
        Gelion\BitrixOptions\Tab::make('first', 'Первая вкладка', [
            Gelion\BitrixOptions\Fields\Text::make('TEXT'),
        ]),
        Gelion\BitrixOptions\Tab::make('second', 'Вторая вкладка', [
            Gelion\BitrixOptions\Fields\Number::make('NUMBER'),
        ]),
    ]);
    ```

### Пример установки в свой модуль для маркетплейса

1. Скопировать пакет к себе в модуль
2. Зарегистрировать классы модуля в файле `include.php`, прописав вручную корректные пути к классам, или загрузив автолоадером. Зарегистрировать вручную можно например так:
    ```php
    Bitrix\Main\Loader::registerAutoLoadClasses('youmodule.id', [
        'Gelion\\BitrixOptions\\Tab'                              => '/lib/Tab.php',
        'Gelion\\BitrixOptions\\Form'                             => '/lib/Form.php',
        'Gelion\\BitrixOptions\\Enums\\Color'                     => '/lib/Enums/Color.php',
        'Gelion\\BitrixOptions\\Enums\\Width'                     => '/lib/Enums/Width.php',
        'Gelion\\BitrixOptions\\Enums\\Height'                    => '/lib/Enums/Height.php',
        'Gelion\\BitrixOptions\\Enums\\Resize'                    => '/lib/Enums/Resize.php',
        'Gelion\\BitrixOptions\\Enums\\Display'                   => '/lib/Enums/Display.php',
        'Gelion\\BitrixOptions\\Enums\\TagColor'                  => '/lib/Enums/TagColor.php',
        'Gelion\\BitrixOptions\\Enums\\AlertIcon'                 => '/lib/Enums/AlertIcon.php',
        'Gelion\\BitrixOptions\\Enums\\AlertSize'                 => '/lib/Enums/AlertSize.php',
        'Gelion\\BitrixOptions\\Enums\\TextStyle'                 => '/lib/Enums/TextStyle.php',
        'Gelion\\BitrixOptions\\Enums\\AlertTextPosition'         => '/lib/Enums/AlertTextPosition.php',
        'Gelion\\BitrixOptions\\Fields\\Date'                     => '/lib/Fields/Date.php',
        'Gelion\\BitrixOptions\\Fields\\Text'                     => '/lib/Fields/Text.php',
        'Gelion\\BitrixOptions\\Fields\\Alert'                    => '/lib/Fields/Alert.php',
        'Gelion\\BitrixOptions\\Fields\\Email'                    => '/lib/Fields/Email.php',
        'Gelion\\BitrixOptions\\Fields\\Number'                   => '/lib/Fields/Number.php',
        'Gelion\\BitrixOptions\\Fields\\Select'                   => '/lib/Fields/Select.php',
        'Gelion\\BitrixOptions\\Fields\\Heading'                  => '/lib/Fields/Heading.php',
        'Gelion\\BitrixOptions\\Fields\\Checkbox'                 => '/lib/Fields/Checkbox.php',
        'Gelion\\BitrixOptions\\Fields\\Textarea'                 => '/lib/Fields/Textarea.php',
        'Gelion\\BitrixOptions\\Fields\\Condition'                => '/lib/Fields/Condition.php',
        'Gelion\\BitrixOptions\\Fields\\HtmlEditor'               => '/lib/Fields/HtmlEditor.php',
        'Gelion\\BitrixOptions\\Fields\\ColorPicker'              => '/lib/Fields/ColorPicker.php',
        'Gelion\\BitrixOptions\\Traits\\WithTag'                  => '/lib/Traits/WithTag.php',
        'Gelion\\BitrixOptions\\Traits\\WithHint'                 => '/lib/Traits/WithHint.php',
        'Gelion\\BitrixOptions\\Traits\\WithColor'                => '/lib/Traits/WithColor.php',
        'Gelion\\BitrixOptions\\Traits\\WithModuleId'             => '/lib/Traits/WithModuleId.php',
        'Gelion\\BitrixOptions\\Traits\\WithStyleClass'           => '/lib/Traits/WithStyleClass.php',
        'Gelion\\BitrixOptions\\Traits\\WithDisplayWithoutValue'  => '/lib/Traits/WithDisplayWithoutValue.php',
    ]);
    ```

## 🧩 Новые типы полей (v2.x)

| Поле      | Назначение                          |
|-----------|-------------------------------------|
| `Date`    | Поле Text с типом date              |
| `Email`   | Поле Text с типом email             |
| `Heading` | Поле, выводящий заголовок           |
| `Select`  | Замена полям Dropdown и Multiselect |

Дополнительные, свои типы полей больше не нужно регистрировать отдельно, можете использовать их непосредственно в
конструкторе формы.

## Схема работы

На странице настроек необходимо вызвать генератор формы, в который передаётся id вашего модуля.

```php
   Gelion\BitrixOptions\Form::make('youmodule.id', []);
```

Вторым параметром в генератор формы передаётся массив табов на странице формы.
Необходимо передать как минимум один таб, для корректной генерации и заполнения страницы.

```php
    Gelion\BitrixOptions\Form::make('youmodule.id', [
       Gelion\BitrixOptions\Tab::make('first', 'Первая вкладка', []),
    ]);
```

В таб, вторым параметром передаётся массив полей для отображения.
В отличие от первой версии пакета - порядок задаётся не полем сортировки, а непосредственно порядком передачи в массиве.

```php
    Gelion\BitrixOptions\Form::make('youmodule.id', [
       Gelion\BitrixOptions\Tab::make('first', 'Первая вкладка', [
         Gelion\BitrixOptions\Fields\Text::make('TEXT'),
      ]),
    ]);
```

## Типы полей

#### Общая информация

Все типы полей отображается через статичный метод `make()`, в который передаётся один или два параметра - ключ в базе
данных и строка для вывода пояснения.
Для типов полей который выводят данные, но ничего не хранят, передаётся один параметр.
В случае передачи одного параметра в поля, которые отображают данные настроек ключ будет использован как строка
пояснения.
Также, все методы, которые наследуются от поля Text имеют общие Fluent методы. Они будут описаны только в текстовом
поле, повторяться не будут.

### Заголовок

Новый тип поля, заменяющий группы полей в предыдущей версии модуля.
Больше нет необходимости делать группировку, просто выведите заголовок в нужном месте, передав в него текст для вывода.

```php
   \Gelion\BitrixOptions\Fields\Heading::make('Я заголовок группы');
```

### Предупреждение

Вывод информационного сообщения, с настраиваемым размером, расположением текста и иконкой.
Поле можно модифицировать через текучий интерфейс, передав в методы Enum необходимых значений свойств.
[документации Bitrix](https://dev.1c-bitrix.ru/api_d7/bitrix/ui/messages/alerts.php)

```php
   \Gelion\BitrixOptions\Fields\Alert::make('Я предупрждение')
        ->setIcon(\Gelion\BitrixOptions\Enums\AlertIcon::INFO)
        ->setSize(\Gelion\BitrixOptions\Enums\AlertSize::XS)
        ->setTextPosition(\Gelion\BitrixOptions\Enums\AlertTextPosition::CENTER);
```

### Текст

Вывод простого текстового поля - базовый компонент для большинства других полей.
Поле можно модифицировать через текучий интерфейс, передав в методы Enum, необходимых значений, сами значения, а иногда
и замыкания в зависимости от типа свойства.

```php
   \Gelion\BitrixOptions\Fields\Text::make('TEXT_OPTION', 'Я текстовое поле')
        ->disabled(true)
        ->readonly(true)
        ->setDefault('default')
        ->setAutocomplete('username')
        ->setHint('Я строка подсказки')
        ->setPlaceholder('Я строка плейсхолдер')
        ->setStyle(\Gelion\BitrixOptions\Enums\TextStyle::ROUND)
        ->setDisplay(\Gelion\BitrixOptions\Enums\Display::BLOCK)
        ->setTag('Я строка тега', \Gelion\BitrixOptions\Enums\TagColor::SUCCESS)
        ->setHeight(\Gelion\BitrixOptions\Enums\Height::MD)
        ->setWidth(\Gelion\BitrixOptions\Enums\Width::W75)
        ->setColor(\Gelion\BitrixOptions\Enums\Color::SUCCESS);
```

### Дата

Вывод поля для даты - текстовое поле, но с типом date.

```php
   \Gelion\BitrixOptions\Fields\Date::make('DATE_OPTION', 'Я поле даты');
```

### Email

Вывод поля для электронной почты - текстовое поле, но с типом email.

```php
   \Gelion\BitrixOptions\Fields\Email::make('EMAIL_OPTION', 'Я поле электронной почты');
```

### Number

Вывод поля для чисел - текстовое поле, но с типом number.
Имеет три дополнительных метода `min()`, `max()`, `step()`.

```php
   \Gelion\BitrixOptions\Fields\Number::make('NUMBER_OPTION', 'Я поле ввода чисел')
        ->min(0)
        ->max(10)
        ->step(2);
```

### Checkbox

Вывод переключателя, основан на текстовом поле.
Хранит значения в базе в формате `Y` и `N`, выводится в виде чекбокса.

```php
   \Gelion\BitrixOptions\Fields\Checkbox::make('CHECKBOX_OPTION', 'Я поле ввода чекбокса');
```

### ColorPicker

Вывод поля для выбора цвета, основано на текстовом поле.
Имеет два дополнительных метода `cols()`, `rows()`.
Обратите внимание - при сохранении страницы, но не выбранном цвете по-умолчанию будет всегда сохраняться чёрный цвет.

```php
   \Gelion\BitrixOptions\Fields\ColorPicker::make('CHECKBOX_OPTION', 'Я поле ввода чекбокса');
```

### Textarea

Поле для ввода многострочного ввода данных, основано на текстовом поле.
На данный момент реализованы все способы изменять состояния поля, но не все они ведут себя корректно.

```php
   \Gelion\BitrixOptions\Fields\Textarea::make('TEXTAREA_OPTION', 'Я поле многостраничного ввода')
        ->setResize(\Gelion\BitrixOptions\Enums\Resize::RESIZE_X)
        ->cols(10)
        ->rows(10);
```

### Select

Селектор для выбора значений из списка, основано на текстовом поле.
Имеет три дополнительных метода `options()` - для передачи опций выбора, `size()` - для передачи атрибута size,
`multiple()` - для включения множественного выбора.

```php
   \Gelion\BitrixOptions\Fields\Select::make('SELECT_OPTION', 'Я поле выбора из списка')
        ->options([
            'key-1' => 'Вариант 1',
            'key-2' => 'Вариант 2',
        ])
        ->size()
        ->multiple();
```

### HtmlEditor

Текстовый редактор, с возможностью переключаться между типами text, html и визуальный редактор.

```php
   \Gelion\BitrixOptions\Fields\HtmlEditor::make('HTML_EDITOR_OPTION', 'Я поле редактора')
        ->setHint('Я строка подсказки');
```

### Condition

Поле выбора условий, основанное на классе CCatalogCondTree.

```php
   \Gelion\BitrixOptions\Fields\Condition::make('CONDITION_OPTION', 'Я поле условия');
```

## 📚 Дополнительно

- Документация по UI Bitrix: [dev.1c-bitrix.ru](https://dev.1c-bitrix.ru/api_d7/bitrix/ui/index.php)