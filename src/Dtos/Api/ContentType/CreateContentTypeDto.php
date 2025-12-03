<?php

namespace Wave8\Factotum\Cms\Dtos\Api\ContentType;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Wave8\Factotum\Cms\Enums\ContentType;

#[MapName(SnakeCaseMapper::class)]
class CreateContentTypeDto extends Data
{
    public function __construct(
        public ContentType $type,
        public string $label,
        public bool $editable,
        public ?string $icon,
        public bool $sitemap,
        public bool $visible,
        public bool $hierarchical,
    ) {}
}
