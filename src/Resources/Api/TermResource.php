<?php

namespace Wave8\Factotum\Cms\Resources\Api;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Resource;
use Wave8\Factotum\Base\Enums\Locale;

#[MapName(SnakeCaseMapper::class)]
class TermResource extends Resource
{
    public function __construct(
        public int $id,
        public int $taxonomyId,
        public Optional|null|int $parentId,
        public string $name,
        public string $slug,
        public Optional|null|string $description,
        public Locale $lang,
        public int $sortOrder,
        public Optional|null|int $depth,
        public Optional|null|string $canonicalUrl,
        #[DataCollectionOf(TermResource::class)]
        public Optional|null|array $children,
        public string $createdAt,
        public string $updatedAt,
    ) {}
}
