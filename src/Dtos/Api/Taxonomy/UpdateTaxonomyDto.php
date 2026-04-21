<?php

namespace Wave8\Factotum\Cms\Dtos\Api\Taxonomy;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class UpdateTaxonomyDto extends Data
{
    public function __construct(
        public Optional|string $label,
        public Optional|bool $isHierarchical,
        public Optional|int $sortOrder,
    ) {}
}
