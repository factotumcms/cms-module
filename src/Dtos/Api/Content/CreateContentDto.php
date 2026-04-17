<?php

namespace Wave8\Factotum\Cms\Dtos\Api\Content;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;
use Wave8\Factotum\Base\Enums\Locale;
use Wave8\Factotum\Cms\Enums\ContentEditorType;
use Wave8\Factotum\Cms\Enums\ContentStatus;
use Wave8\Factotum\Cms\Resources\Models\Content\ContentSeoParamsResource;
use Wave8\Factotum\Cms\Resources\Models\Content\ContentSocialParamsResource;

#[MapName(SnakeCaseMapper::class)]
class CreateContentDto extends Data
{
    public string $absUrl;

    public function __construct(
        public ContentStatus $status,
        public string $title,
        public ContentEditorType $editorType,
        public string $content,
        public string $url,
        public Locale $lang,
        public Optional|bool $showInMenu = false,
        public Optional|bool $isHome = false,
        public Optional|int $orderNo = 1,
        public Optional|bool $isVisible = true,
        public Optional|null|int $userId = null,
        public Optional|null|int $parentId = null,
        public ContentSeoParamsResource $seoParams = new ContentSeoParamsResource,
        public ContentSocialParamsResource $socialParams = new ContentSocialParamsResource,
    ) {
        $this->absUrl = '/'.$this->lang->value.'/'.$this->url;
    }
}
