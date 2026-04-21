<?php

namespace Wave8\Factotum\Cms\Resources\Api;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
class TaxonomyResource extends Resource
{
    public function __construct(
        public int $id,
        public string $name,
        public string $label,
        public bool $isHierarchical,
        public int $sortOrder,
    ) {}
}
