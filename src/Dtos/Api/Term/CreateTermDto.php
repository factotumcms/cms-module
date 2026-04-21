<?php

namespace Wave8\Factotum\Cms\Dtos\Api\Term;

use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;
use Wave8\Factotum\Base\Enums\Locale;

#[MapName(SnakeCaseMapper::class)]
class CreateTermDto extends Data
{
    public function __construct(
        public string $name,
        public Locale $lang,
        public Optional|string $slug = '',
        public Optional|null|string $description = null,
        public Optional|null|int $parentId = null,
        public Optional|int $sortOrder = 0,
    ) {
        $this->slug = ! empty($this->slug)
            ? Str::slug($this->slug)
            : Str::slug($this->name);
    }
}
