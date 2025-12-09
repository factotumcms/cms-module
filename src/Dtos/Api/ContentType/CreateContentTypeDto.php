<?php

namespace Wave8\Factotum\Cms\Dtos\Api\ContentType;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;
use Wave8\Factotum\Cms\Enums\ContentType as ContentTypeEnum;

#[MapName(SnakeCaseMapper::class)]
class CreateContentTypeDto extends Data
{
    public function __construct(
        public ContentTypeEnum $type,
        public string $label,
        public bool $editable,
        public ?string $icon,
        public bool $sitemap,
        public bool $visible,
        public bool $hierarchical,
        public Optional|null|int $orderNo = null,
    ) {}
}
