<?php

namespace Wave8\Factotum\Cms\Dtos\Api\ContentField;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;
use Wave8\Factotum\Cms\Enums\ContentFieldType;

#[MapName(SnakeCaseMapper::class)]
class CreateContentFieldDto extends Data
{
    public function __construct(
        public string $name,
        public string $label,
        public ContentFieldType $type,
        public bool $mandatory = false,
        public Optional|array|null $options = null,
        public Optional|array $visibilityRules = [],
        public Optional|int|null $minWidthSize = null,
        public Optional|int|null $minHeightSize = null,
        public Optional|int|null $maxFileSize = null,
        public Optional|string|null $imageOperation = null,
        public Optional|array|null $allowedTypes = null,
        public Optional|array|null $resizes = null,
    ) {}
}
