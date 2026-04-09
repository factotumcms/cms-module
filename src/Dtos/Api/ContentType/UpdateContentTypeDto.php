<?php

namespace Wave8\Factotum\Cms\Dtos\Api\ContentType;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class UpdateContentTypeDto extends Data
{
    public function __construct(
        // Safe fields
        public Optional|string $label,
        public Optional|int $orderNo,
        public Optional|string $icon,
        public Optional|bool $sitemap,
        public Optional|bool $visible,

        // Critical fields
        public Optional|string $type,
        public Optional|bool $hierarchical,
    ) {}
}
