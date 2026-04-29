<?php

namespace Wave8\Factotum\Cms\Dtos\Api\UrlAlias;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Wave8\Factotum\Base\Enums\Locale;

#[MapName(SnakeCaseMapper::class)]
class CreateUrlAliasDto extends Data
{
    public function __construct(
        public string $uri,
        public string $routableType,
        public int $routableId,
        public Locale $locale,
        public bool $isCanonical = true,
    ) {
        $this->uri = '/'.trim($this->uri, '/');
    }
}

