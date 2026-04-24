<?php

namespace Wave8\Factotum\Cms\Dtos\Api\Term;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class UpdateTermDto extends Data
{
    public function __construct(
        public Optional|string $name,
        public Optional|string $slug,
        public Optional|null|string $description,
        public ?int $parentId,
        public Optional|int $sortOrder,
    ) {}
}
