<?php

namespace Wave8\Factotum\Cms\Rules\Content;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Wave8\Factotum\Cms\Enums\ContentEditorType;

class ContentEditorTypeRule implements ValidationRule
{
    public array $contentEditorTypes;

    public function __construct()
    {
        $this->contentEditorTypes = ContentEditorType::getValues()->toArray();
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! in_array($value, $this->contentEditorTypes)) {
            $fail(__("validation.content_editor_type_rule", [
                'attribute' => $attribute,
                'values' => implode(', ', $this->contentEditorTypes)
            ]));
        }
    }
}
