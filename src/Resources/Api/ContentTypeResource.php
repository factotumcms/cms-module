<?php

namespace Wave8\Factotum\Cms\Resources\Api;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
class ContentTypeResource extends Resource
{
    public function __construct(
        public int $id,
        public string $label,
        public string $type,
        public bool $editable,
        public int $orderNo,
        public Optional|string $icon,
        public bool $sitemap,
        public bool $visible,
        public bool $hierarchical,
    ) {}
}
