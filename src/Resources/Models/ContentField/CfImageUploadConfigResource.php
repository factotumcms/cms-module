<?php

namespace Wave8\Factotum\Cms\Resources\Models\ContentField;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
class CfImageUploadConfigResource extends Resource
{
    public function __construct(
        public ?int $minWidthSize = null,
        public ?int $minHeightSize = null,
        public ?int $maxFileSize = null,
        public ?string $imageOperation = null,
        public array $allowedTypes = ['*'],
        public array $resizes = [],
    ) {}
}
