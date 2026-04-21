<?php

namespace Wave8\Factotum\Cms\Dtos\Api\Translation;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Wave8\Factotum\Base\Enums\Locale;

#[MapName(SnakeCaseMapper::class)]
class LinkTranslationDto extends Data
{
    public function __construct(
        public int $sourceId,
        public string $sourceType,
        public int $targetId,
        public string $targetType,
        public Locale $sourceLocale,
        public Locale $targetLocale,
    ) {}
}
