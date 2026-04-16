<?php

namespace Wave8\Factotum\Cms\Resources\Api;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;
use Wave8\Factotum\Cms\Enums\ContentFieldType;
use Wave8\Factotum\Cms\Resources\Models\ContentField\CfConfigResource;

#[MapName(SnakeCaseMapper::class)]
class ContentFieldResource extends Resource
{
    public function __construct(
        public int $id,
        public string $name,
        public string $label,
        public ContentFieldType $type,
        public CfConfigResource $configs
    ) {}
}
