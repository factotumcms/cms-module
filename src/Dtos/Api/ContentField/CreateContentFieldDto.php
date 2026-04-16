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
        public Optional|int $orderNo = 1,
        public CfConfigResource $configs = new CfConfigResource,
    ) {
        $this->name = Str::lower(Str::snake($this->name));
    }
}
