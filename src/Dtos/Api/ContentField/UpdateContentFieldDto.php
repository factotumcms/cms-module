<?php

namespace Wave8\Factotum\Cms\Dtos\Api\ContentField;

use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;
use Wave8\Factotum\Cms\Enums\ContentFieldType;

#[MapName(SnakeCaseMapper::class)]
class UpdateContentFieldDto extends Data
{
    public function __construct(
        // Safe fields
        public Optional|string $label,
        public Optional|ContentFieldType $type,
        public Optional|int $orderNo,
        public Optional|array|null $configs = null,

        // Critical fields
        public Optional|string $name,
    ) {
        $this->name = Str::lower(Str::snake($this->name));
    }
}
