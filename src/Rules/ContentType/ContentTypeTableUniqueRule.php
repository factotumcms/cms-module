<?php

namespace Wave8\Factotum\Cms\Rules\ContentType;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Schema;

class ContentTypeTableUniqueRule implements ValidationRule
{
    private array $tableListing;

    public function __construct()
    {
        $this->tableListing = collect(Schema::getTables(Schema::getCurrentSchemaListing()))->pluck('name')->toArray();
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (in_array($value, $this->tableListing)) {
            $fail(__('validation.content_type_table_unique_rule'));
        }
    }
}
