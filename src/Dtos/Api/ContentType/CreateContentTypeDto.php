<?php

namespace Wave8\Factotum\Cms\Dtos\Api\ContentType;

use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class CreateContentTypeDto extends Data
{
    public function __construct(
        public string $type,
        public string $label,
        public bool $editable,
        public ?string $icon,
        public bool $sitemap,
        public bool $visible,
        public bool $hierarchical,
        public Optional|int $orderNo = 1,
    ) {
        $this->type = Str::lower(Str::snake($this->type));
    }
}
