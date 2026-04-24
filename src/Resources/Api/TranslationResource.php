<?php

namespace Wave8\Factotum\Cms\Resources\Api;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;
use Wave8\Factotum\Base\Enums\Locale;

#[MapName(SnakeCaseMapper::class)]
class TranslationResource extends Resource
{
    public function __construct(
        public int $id,
        public string $translationGroup,
        public string $translatableType,
        public int $translatableId,
        public Locale $locale,
    ) {}
}
