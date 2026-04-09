<?php

namespace Wave8\Factotum\Cms\Dtos\Api\ContentType;

use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class UpdateContentTypeDto extends Data
{
    public function __construct(
        public Optional|string $label,
    ) {}
}
