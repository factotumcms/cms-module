<?php

namespace Wave8\Factotum\Cms\Dtos\Api\ContentField;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class CreateContentFieldDto extends Data
{
    public function __construct(
        public string $name,
        public string $label,
        public string $type,
        public bool $mandatory = false,
        public Optional|array|null $options = null,
        public Optional|array|null $visibilityRules = null,
        public Optional|int|null $minWidthSize = null,
        public Optional|int|null $minHeightSize = null,
        public Optional|int|null $maxFileSize = null,
        public Optional|string|null $imageOperation = null,
    ) {}
}
