<?php

namespace Wave8\Factotum\Cms\Resources\Models\Content;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
class ContentSeoParamsResource extends Resource
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $canonicalUrl = null,
        public ?string $robotsIndexing = null,
        public ?string $robotsFollowing = null,
        public ?string $focusKey = null,
    ) {}
}
