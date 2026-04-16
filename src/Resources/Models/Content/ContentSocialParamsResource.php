<?php

namespace Wave8\Factotum\Cms\Resources\Models\Content;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
class ContentSocialParamsResource extends Resource
{
    public function __construct(
        public ?string $fbTitle = null,
        public ?string $fbDescription = null,
        public ?string $fbImage = null,
    ) {}
}
