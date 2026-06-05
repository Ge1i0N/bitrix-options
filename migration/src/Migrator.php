<?php

declare(strict_types=1);

namespace Gelion\BitrixOptionsMigration;

final class Migrator
{
    private const TYPE_MAP = [
        'STRING' => 'Text',
        'NUMBER' => 'Number',
        'TEXTAREA' => 'Textarea',
        'CHECKBOX' => 'Checkbox',
        'COLORPICKER' => 'ColorPicker',
        'DROPDOWN' => 'Select',
        'MULTISELECT' => 'Select',  // Special: needs ->multiple()
        'HTMLEDITOR' => 'HtmlEditor',
        'CONDITIONS' => 'Condition',
        'ALERT' => 'Alert',  // Special: no key
    ];

    private const PARAM_MAP = [
        'DEFAULT' => 'setDefault',
        'NOTES' => 'setHint',
        'PLACEHOLDER' => 'setPlaceholder',
        'TAG' => 'setTag',  // Special: needs TagColor enum
        'READONLY' => 'readonly',
        'DISABLED' => 'disabled',
        'AUTOCOMPLETE' => 'setAutocomplete',
        'STEP' => 'step',    // NUMBER only
        'MIN' => 'min',      // NUMBER only
        'MAX' => 'max',      // NUMBER only
        'COLS' => 'cols',    // TEXTAREA only
        'ROWS' => 'rows',    // TEXTAREA only
        'SIZE' => 'size',        // SELECT types
        'OPTIONS' => 'options',  // SELECT types - needs transformation
    ];

    // Type-specific parameter validation: which params are valid for which types
    private const TYPE_SPECIFIC_PARAMS = [
        'NUMBER' => ['STEP', 'MIN', 'MAX'],
        'TEXTAREA' => ['COLS', 'ROWS'],
        'DROPDOWN' => ['OPTIONS', 'SIZE'],
        'MULTISELECT' => ['OPTIONS', 'SIZE'],
    ];

    private array $warnings = [];

    public function getFieldClass(string $v1Type): ?string
    {
        return self::TYPE_MAP[$v1Type] ?? null;
    }

    public function mapParameter(string $param): ?string
    {
        return self::PARAM_MAP[$param] ?? null;
    }

    public function isTypeSpecificParam(string $param): bool
    {
        foreach (self::TYPE_SPECIFIC_PARAMS as $typeParams) {
            if (in_array($param, $typeParams, true)) {
                return true;
            }
        }
        return false;
    }

    public function isValidForType(string $param, string $v1Type): bool
    {
        if (!isset(self::PARAM_MAP[$param])) {
            return false;
        }

        $specificParams = self::TYPE_SPECIFIC_PARAMS[$v1Type] ?? [];

        if (in_array($param, ['STEP', 'MIN', 'MAX', 'COLS', 'ROWS', 'SIZE'], true)) {
            return in_array($param, $specificParams, true);
        }

        return true;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    private function addWarning(string $type, string $message, array $context = []): void
    {
        $this->warnings[] = [
            'type' => $type,
            'message' => $message,
            'context' => $context,
        ];
    }

    public function convert(array $v1Config): MigrationResult
    {
        $this->warnings = [];

        // 1. Validate
        if (!isset($v1Config['module_id']) || !is_string($v1Config['module_id'])) {
            $this->addWarning('missing_module_id', 'Конфигурация должна содержать строку module_id');
            return new MigrationResult(success: false, warnings: $this->warnings);
        }
        if (!isset($v1Config['tabs']) || !is_array($v1Config['tabs'])) {
            $this->addWarning('missing_tabs', 'Конфигурация должна содержать массив tabs');
            return new MigrationResult(success: false, warnings: $this->warnings);
        }

        $moduleId = $v1Config['module_id'];
        $tabStrings = [];
        $usedClasses = ['Form', 'Tab'];  // Always needed

        // 2. Process tabs
        foreach ($v1Config['tabs'] as $tabIndex => $tab) {
            $tabStrings[] = $this->buildTabCode($tab, $usedClasses, $tabIndex);
        }

        // 3. Generate code
        $imports = $this->generateImportList($usedClasses);
        $code = $this->buildCodeString($moduleId, $tabStrings, $imports);

        return new MigrationResult(
            code: $code,
            warnings: $this->warnings,
            imports: $imports,
            success: true,
        );
    }

    private function buildTabCode(array $tab, array &$usedClasses, int $tabIndex): string
    {
        $tabId = $tab['DIV'] ?? ('tab_' . $tabIndex);
        $tabTitle = $tab['TAB'] ?? $tabId;
        $fieldCodes = $this->convertTab($tab, $usedClasses);

        $fields = '';
        if (!empty($fieldCodes)) {
            $fields = "\n";
            foreach ($fieldCodes as $fieldCode) {
                $fields .= "        {$fieldCode}\n";
            }
        }

        return "    Tab::make(" . var_export($tabId, true) . ", " . var_export($tabTitle, true) . ", [{$fields}    ])";
    }

    private function convertTab(array $tab, array &$usedClasses): array
    {
        $codes = [];
        $groups = $tab['GROUPS'] ?? [];

        if (empty($groups) && isset($tab['OPTIONS'])) {
            // Direct options without groups
            foreach ($tab['OPTIONS'] as $key => $field) {
                $codes[] = $this->convertField($key, $field, $usedClasses);
            }
            return $codes;
        }

        foreach ($groups as $group) {
            $groupCodes = $this->convertGroup($group, $usedClasses);
            $codes = array_merge($codes, $groupCodes);
        }

        return $codes;
    }

    private function convertGroup(array $group, array &$usedClasses): array
    {
        $codes = [];
        $options = $group['OPTIONS'] ?? [];

        if (empty($options)) {
            return $codes;
        }

        // Add Heading if group has title
        if (!empty($group['TITLE'])) {
            $codes[] = "Heading::make(" . var_export($group['TITLE'], true) . "),";
            $usedClasses[] = 'Heading';
        }

        // Sort options by SORT value
        $sortedOptions = $this->sortFields($options);

        foreach ($sortedOptions as $key => $field) {
            $codes[] = $this->convertField($key, $field, $usedClasses);
        }

        return $codes;
    }

    private function sortFields(array $fields): array
    {
        $indexed = [];
        foreach ($fields as $key => $field) {
            $indexed[] = [
                'key' => $key,
                'field' => $field,
                'index' => count($indexed),
            ];
        }

        usort($indexed, function (array $a, array $b) {
            $sortA = $a['field']['SORT'] ?? 500;
            $sortB = $b['field']['SORT'] ?? 500;

            if ($sortA === $sortB) {
                return $a['index'] - $b['index'];
            }

            return $sortA < $sortB ? -1 : 1;
        });

        $result = [];
        foreach ($indexed as $item) {
            $result[$item['key']] = $item['field'];
        }

        return $result;
    }

    private function convertField(string $key, array $field, array &$usedClasses): string
    {
        $type = $field['TYPE'] ?? 'STRING';
        $title = $field['TITLE'] ?? '';
        $className = $this->getFieldClass($type);

        if ($className === null) {
            $this->addWarning('unknown_field_type', "Неизвестный тип поля: {$type}", [
                'key' => $key,
                'type' => $type,
            ]);
            return "// {$key}: Неизвестный тип '{$type}' - требуется ручная миграция";
        }

        $usedClasses[] = $className;

        return match ($type) {
            'STRING' => $this->convertTextField($key, $field, $usedClasses),
            'NUMBER' => $this->convertNumberField($key, $field, $usedClasses),
            'CHECKBOX' => $this->convertCheckboxField($key, $field, $usedClasses),
            'TEXTAREA' => $this->convertTextareaField($key, $field, $usedClasses),
            'DROPDOWN' => $this->convertDropdownField($key, $field, $usedClasses),
            'MULTISELECT' => $this->convertMultiSelectField($key, $field, $usedClasses),
            'COLORPICKER' => $this->convertColorPickerField($key, $field, $usedClasses),
            'HTMLEDITOR' => $this->convertHtmlEditorField($key, $field, $usedClasses),
            'CONDITIONS' => $this->convertConditionField($key, $field, $usedClasses),
            'ALERT' => $this->convertAlertField($field, $usedClasses),
            default => "{$className}::make(" . var_export($key, true) . ", " . var_export($title, true) . "),",
        };
    }

    private function convertTextField(string $key, array $field, array &$usedClasses): string
    {
        $base = "Text::make(" . var_export($key, true) . ", " . var_export($field['TITLE'] ?? '', true) . ")";
        return $this->addParameters('STRING', $base, $key, $field, $usedClasses) . ",";
    }

    private function convertNumberField(string $key, array $field, array &$usedClasses): string
    {
        $base = "Number::make(" . var_export($key, true) . ", " . var_export($field['TITLE'] ?? '', true) . ")";
        return $this->addParameters('NUMBER', $base, $key, $field, $usedClasses) . ",";
    }

    private function convertCheckboxField(string $key, array $field, array &$usedClasses): string
    {
        $base = "Checkbox::make(" . var_export($key, true) . ", " . var_export($field['TITLE'] ?? '', true) . ")";
        return $this->addParameters('CHECKBOX', $base, $key, $field, $usedClasses) . ",";
    }

    private function convertTextareaField(string $key, array $field, array &$usedClasses): string
    {
        $base = "Textarea::make(" . var_export($key, true) . ", " . var_export($field['TITLE'] ?? '', true) . ")";
        return $this->addParameters('TEXTAREA', $base, $key, $field, $usedClasses) . ",";
    }

    private function convertDropdownField(string $key, array $field, array &$usedClasses): string
    {
        $base = "Select::make(" . var_export($key, true) . ", " . var_export($field['TITLE'] ?? '', true) . ")";
        return $this->addParameters('DROPDOWN', $base, $key, $field, $usedClasses) . ",";
    }

    private function convertMultiSelectField(string $key, array $field, array &$usedClasses): string
    {
        $base = "Select::make(" . var_export($key, true) . ", " . var_export($field['TITLE'] ?? '', true) . ")->multiple()";
        return $this->addParameters('MULTISELECT', $base, $key, $field, $usedClasses) . ",";
    }

    private function convertColorPickerField(string $key, array $field, array &$usedClasses): string
    {
        $base = "ColorPicker::make(" . var_export($key, true) . ", " . var_export($field['TITLE'] ?? '', true) . ")";
        return $this->addParameters('COLORPICKER', $base, $key, $field, $usedClasses) . ",";
    }

    private function convertHtmlEditorField(string $key, array $field, array &$usedClasses): string
    {
        $base = "HtmlEditor::make(" . var_export($key, true) . ", " . var_export($field['TITLE'] ?? '', true) . ")";
        return $this->addParameters('HTMLEDITOR', $base, $key, $field, $usedClasses) . ",";
    }

    private function convertConditionField(string $key, array $field, array &$usedClasses): string
    {
        $base = "Condition::make(" . var_export($key, true) . ", " . var_export($field['TITLE'] ?? '', true) . ")";
        return $this->addParameters('CONDITIONS', $base, $key, $field, $usedClasses) . ",";
    }

    /** ALERT has no key parameter - only title text */
    private function convertAlertField(array $field, array &$usedClasses): string
    {
        $base = "Alert::make(" . var_export($field['TITLE'] ?? '', true) . ")";
        return $this->addParameters('ALERT', $base, '', $field, $usedClasses) . ",";
    }

    /**
     * Add fluent method chains for standard and type-specific parameters.
     */
    private function addParameters(string $type, string $baseCall, string $key, array $field, array &$usedClasses): string
    {
        $methods = [];

        // --- Standard parameters (Task 18) ---
        if (isset($field['DEFAULT']) && $field['DEFAULT'] !== '') {
            $methods[] = "->setDefault(" . var_export($field['DEFAULT'], true) . ")";
        }

        if (isset($field['NOTES']) && $field['NOTES'] !== '') {
            $methods[] = "->setHint(" . var_export($field['NOTES'], true) . ")";
        }

        if (isset($field['PLACEHOLDER']) && $field['PLACEHOLDER'] !== '') {
            $methods[] = "->setPlaceholder(" . var_export($field['PLACEHOLDER'], true) . ")";
        }

        if (!empty($field['READONLY'])) {
            $methods[] = "->readonly(true)";
        }

        if (!empty($field['DISABLED'])) {
            $methods[] = "->disabled(true)";
        }

        if (isset($field['AUTOCOMPLETE']) && $field['AUTOCOMPLETE'] !== '') {
            $methods[] = "->setAutocomplete(" . var_export($field['AUTOCOMPLETE'], true) . ")";
        }

        // TAG + TAGCOLOR combined
        if (isset($field['TAG']) && $field['TAG'] !== '') {
            $color = $field['TAGCOLOR'] ?? 'default';
            $tagColorMap = [
                'primary' => 'PRIMARY',
                'success' => 'SUCCESS',
                'danger' => 'DANGER',
                'warning' => 'WARNING',
                'info' => 'INFO',
            ];
            $enumCase = $tagColorMap[strtolower($color)] ?? 'DEFAULT';
            $methods[] = "->setTag(" . var_export($field['TAG'], true) . ", \Gelion\BitrixOptions\Enums\TagColor::{$enumCase})";
            $usedClasses[] = 'TagColor';
        }

        // --- Type-specific parameters (Task 19) ---
        if ($type === 'NUMBER') {
            if (isset($field['STEP'])) {
                $methods[] = "->step({$field['STEP']})";
            }
            if (isset($field['MIN'])) {
                $methods[] = "->min({$field['MIN']})";
            }
            if (isset($field['MAX'])) {
                $methods[] = "->max({$field['MAX']})";
            }
        }

        if ($type === 'TEXTAREA') {
            if (isset($field['COLS'])) {
                $methods[] = "->cols({$field['COLS']})";
            }
            if (isset($field['ROWS'])) {
                $methods[] = "->rows({$field['ROWS']})";
            }
        }

        // DROPDOWN / MULTISELECT (Task 19 + 20)
        if (in_array($type, ['DROPDOWN', 'MULTISELECT'], true)) {
            if (isset($field['SIZE'])) {
                $methods[] = "->size({$field['SIZE']})";
            }
            if (!empty($field['OPTIONS'])) {
                $methods[] = "->options(" . $this->transformOptions($field['OPTIONS']) . ")";
            }
        }

        // Warn on type-specific param used on wrong field type (Task 21)
        $validForType = self::TYPE_SPECIFIC_PARAMS[$type] ?? [];
        foreach ($field as $param => $value) {
            if (!isset(self::PARAM_MAP[$param])) {
                continue; // Unknown params handled below
            }
            // Check if it's a type-specific param (belongs to ANY type)
            $isTypeSpecific = false;
            foreach (self::TYPE_SPECIFIC_PARAMS as $typeParams) {
                if (in_array($param, $typeParams, true)) {
                    $isTypeSpecific = true;
                    break;
                }
            }
            if (!$isTypeSpecific) {
                continue; // Standard param, skip
            }
            // It's a type-specific param - is it valid for this type?
            if (!in_array($param, $validForType, true)) {
                $this->addWarning('parameter_type_mismatch', "{$param} недопустим для типа {$type}", ['key' => $key]);
            }
        }

        // --- Unknown parameter warnings (Task 21) ---
        $structuralParams = ['TYPE', 'TITLE', 'SORT', 'TAGCOLOR'];
        foreach ($field as $param => $value) {
            if (in_array($param, $structuralParams, true)) {
                continue;
            }
            if (isset(self::PARAM_MAP[$param])) {
                continue;
            }
            $this->addWarning('unknown_parameter', "Неизвестный параметр: {$param}", [
                'key' => $key,
                'param' => $param,
            ]);
        }

        if (empty($methods)) {
            return $baseCall;
        }

        if (count($methods) === 1) {
            $fullLine = $baseCall . $methods[0];
            if (strlen($fullLine) < 100) {
                return $fullLine;
            }
        }

        // Multi-line fluent chain (2+ methods, or 1 method exceeding 100 chars)
        $result = $baseCall . "\n";
        foreach ($methods as $i => $method) {
            $result .= "            " . $method;
            if ($i < count($methods) - 1) {
                $result .= "\n";
            }
        }
        return $result;
    }

    /**
     * Transform v1 OPTIONS format to v2 PHP array string.
     *
     * v1: [['TITLE' => 'Label', 'VALUE' => 'val-1'], ...]
     * v2: ['val-1' => 'Label 1', ...]
     */
    private function transformOptions(array $v1Options): string
    {
        $pairs = [];
        $hasInvalid = false;

        foreach ($v1Options as $option) {
            if (!is_array($option) || !isset($option['VALUE'])) {
                $hasInvalid = true;
                continue;
            }
            $value = $option['VALUE'];
            $title = $option['TITLE'] ?? $value;
            $pairs[] = var_export($value, true) . ' => ' . var_export($title, true);
        }

        if ($hasInvalid) {
            $this->addWarning('invalid_options_format', 'Некоторые записи options не имеют VALUE или имеют неверный формат');
        }

        return '[' . implode(', ', $pairs) . ']';
    }

    private function generateImportList(array $usedClasses): array
    {
        $usedClasses = array_unique($usedClasses);

        $main = [];
        $fields = [];
        $enums = [];

        foreach ($usedClasses as $class) {
            if ($class === 'Form' || $class === 'Tab') {
                $main[] = $class;
            } elseif ($class === 'TagColor') {
                $enums[] = $class;
            } else {
                $fields[] = $class;
            }
        }

        sort($main);
        sort($fields);
        sort($enums);

        $imports = [];
        foreach ($main as $class) {
            $imports[] = "use Gelion\\BitrixOptions\\{$class};";
        }
        foreach ($fields as $class) {
            $imports[] = "use Gelion\\BitrixOptions\\Fields\\{$class};";
        }
        foreach ($enums as $class) {
            $imports[] = "use Gelion\\BitrixOptions\\Enums\\{$class};";
        }

        return $imports;
    }

    private function buildCodeString(string $moduleId, array $tabStrings, array $imports): string
    {
        $code = "<?php\n\ndeclare(strict_types=1);\n\n";
        $code .= implode("\n", $imports) . "\n\n";
        $code .= "Form::make(" . var_export($moduleId, true) . ", [\n";
        $code .= implode(",\n", $tabStrings) . ",\n";
        $code .= "]);\n";

        return $code;
    }
}
