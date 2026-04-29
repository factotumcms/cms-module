<?php

namespace Wave8\Factotum\Cms\Resources\Api;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Resource;
use Wave8\Factotum\Base\Enums\Locale;

#[MapName(SnakeCaseMapper::class)]
class UrlAliasResource extends Resource
{
    public function __construct(
        public int $id,
        public string $uri,
        public string $routableType,
        public int $routableId,
        public Locale $locale,
        public bool $isCanonical,
        public Optional|null|string $redirectTo,
        public string $createdAt,
        public string $updatedAt,
    ) {}
}
