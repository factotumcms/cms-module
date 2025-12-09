<?php

namespace Wave8\Factotum\Cms\Dtos\Api\Content;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;
use Wave8\Factotum\Base\Enums\Locale;
use Wave8\Factotum\Cms\Enums\ContentEditorType;
use Wave8\Factotum\Cms\Enums\ContentStatus;

#[MapName(SnakeCaseMapper::class)]
class CreateContentDto extends Data
{
    public string $absUrl;

    public function __construct(
        public int $contentTypeId,
        public ContentStatus $status,
        public string $title,
        public ContentEditorType $editorType,
        public string $content,
        public string $url,
        public Locale $lang,
        public Optional|bool $isHome = false,
        public Optional|null|int $userId = null,
        public Optional|null|int $parentId = null,
    ) {
        $this->absUrl = '/'.$this->lang->value.'/'.$this->url;
    }
}
