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
            $fail('The :attribute is invalid, a database table with this name already exists.');
        }
    }
}
