<?php

namespace Wave8\Factotum\Cms\Resources\Models\ContentField;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
class CfConfigResource extends Resource
{
    public function __construct(
        public Optional|CfSelectConfigResource $select,
        public Optional|array $text,
        public Optional|array $number,
        public Optional|array $url,
        public Optional|array $textarea,
        public Optional|array $checkbox,
        public Optional|array $imageUpload,
        public Optional|array $linkedContent,
        public Optional|array $multipleLinkedContent
    ) {}
}
