<?php

namespace Wave8\Factotum\Cms\Dtos\Api\UrlAlias;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class UpdateUrlAliasDto extends Data
{
    public function __construct(
        public Optional|string $uri = '',
        public Optional|null|string $redirectTo = null,
        public Optional|bool $isCanonical = true,
    ) {
        if ($this->uri instanceof Optional === false && ! empty($this->uri)) {
            $this->uri = '/'.trim($this->uri, '/');
        }
    }
}
