<?php

namespace Wave8\Factotum\Cms\Resources\Api;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;
use Wave8\Factotum\Base\Enums\Locale;
use Wave8\Factotum\Cms\Enums\ContentEditorType;
use Wave8\Factotum\Cms\Enums\ContentStatus;
use Wave8\Factotum\Cms\Resources\Models\Content\ContentSeoParamsResource;
use Wave8\Factotum\Cms\Resources\Models\Content\ContentSocialParamsResource;

#[MapName(SnakeCaseMapper::class)]
class ContentResource extends Resource
{
    public function __construct(
        public int $id,
        public ContentStatus $status,
        public string $title,
        public ContentEditorType $editor_type,
        public string $content,
        public string $url,
        public string $absUrl,
        public Locale $lang,
        public bool $showInMenu,
        public bool $isHome,
        public bool $isVisible,
        public int $orderNo,
        public ContentSeoParamsResource $seoParams,
        public ContentSocialParamsResource $socialParams,
        public string $createdAt,
        public string $updatedAt,
    ) {}
}
