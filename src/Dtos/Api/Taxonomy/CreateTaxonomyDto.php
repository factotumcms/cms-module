<?php

namespace Wave8\Factotum\Cms\Dtos\Api\Taxonomy;

use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class CreateTaxonomyDto extends Data
{
    public function __construct(
        public string $name,
        public string $label,
        public Optional|bool $isHierarchical = false,
        public Optional|int $sortOrder = 0,
    ) {
        $this->name = Str::lower(Str::snake($this->name));
    }
}
