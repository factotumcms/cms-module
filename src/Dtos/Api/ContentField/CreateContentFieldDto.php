<?php

namespace Wave8\Factotum\Cms\Dtos\Api\ContentField;

use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;
use Wave8\Factotum\Cms\Enums\ContentFieldType;
use Wave8\Factotum\Cms\Resources\Models\ContentField\CfConfigResource;

#[MapName(SnakeCaseMapper::class)]
class CreateContentFieldDto extends Data
{
    public function __construct(
        public string $name,
        public string $label,
        public ContentFieldType $type,
        public bool $mandatory = false,
        public bool $readonly = false,
        public Optional|string|null $hint = null,
        public Optional|int $orderNo = 1,
        public Optional|CfConfigResource|null $configs = null,
        public Optional|array|null $visibilityRules = null,
        public Optional|array|null $mandatoryRules = null,
    ) {
        $this->name = Str::lower(Str::snake($this->name));
    }
}
