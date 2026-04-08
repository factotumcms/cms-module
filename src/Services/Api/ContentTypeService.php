<?php

namespace Wave8\Factotum\Cms\Services\Api;

use AllowDynamicProperties;
use Illuminate\Filesystem\Filesystem;
use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\ContentField\CreateContentFieldDto;
use Wave8\Factotum\Cms\Dtos\Api\ContentType\CreateContentTypeDto;
use Wave8\Factotum\Cms\Enums\BaseContentType as ContentTypeEnum;
use Wave8\Factotum\Cms\Models\ContentField;
use Wave8\Factotum\Cms\Models\ContentType;

#[AllowDynamicProperties]
class ContentTypeService implements ContentTypeServiceInterface
{
    public function __construct(public readonly ContentType $model)
    {
        $this->fs = app(Filesystem::class);
    }

    public function single(int $id): ContentType
    {
        return $this->model::findOrFail($id);
    }

    public function create(CreateContentTypeDto $data): ContentType
    {
        return $this->model::create($data->toArray());
    }

    public function getByType(ContentTypeEnum|string $type): ContentType
    {
        return $this->model::where('type', $type)->firstOrFail();
    }

    public function createFieldForContentType(ContentType $contentType, CreateContentFieldDto $data): ContentField
    {
        return $contentType->contentFields()->create(
            $data->toArray()
        );
    }
}
