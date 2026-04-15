<?php

namespace Wave8\Factotum\Cms\Resources\Models\ContentField;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
class CfSelectConfigResource extends Resource
{
    public function __construct(
        public array $options,
    ) {}
}
