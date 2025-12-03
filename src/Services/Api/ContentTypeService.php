<?php

namespace Wave8\Factotum\Cms\Services\Api;

use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\ContentField\CreateContentFieldDto;
use Wave8\Factotum\Cms\Dtos\Api\ContentType\CreateContentTypeDto;
use Wave8\Factotum\Cms\Enums\ContentType as ContentTypeEnum;
use Wave8\Factotum\Cms\Models\ContentField;
use Wave8\Factotum\Cms\Models\ContentType;

class ContentTypeService implements ContentTypeServiceInterface
{
    public function __construct(public readonly ContentType $model) {}

    public function single(int $id): ContentType
    {
        return $this->model::findOrFail($id);
    }

    public function getByType(ContentTypeEnum $type): ContentType
    {
        return $this->model::where('type', $type)->firstOrFail();
    }

    public function create(CreateContentTypeDto $data): ContentType
    {
        return $this->model::create($data->toArray());
    }

    public function createFieldForContentType(ContentTypeEnum $type, CreateContentFieldDto $data): ContentField
    {
        $contentType = $this->getByType($type);

        return $contentType->content_fields()->create(
            $data->toArray()
        );
    }
}
