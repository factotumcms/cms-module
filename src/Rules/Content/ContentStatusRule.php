<?php

namespace Wave8\Factotum\Cms\Rules\Content;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Wave8\Factotum\Cms\Enums\ContentStatus;

class ContentStatusRule implements ValidationRule
{
    private array $contentStatuses;

    public function __construct()
    {
        $this->contentStatuses = ContentStatus::getValues()->toArray();
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! in_array($value, $this->contentStatuses)) {
            $fail('The :attribute is invalid, must be one of: '.implode(', ', $this->contentStatuses).'.');
        }
    }
}
