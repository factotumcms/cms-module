<?php

namespace Wave8\Factotum\Cms\Services\Api;

use AllowDynamicProperties;
use Illuminate\Filesystem\Filesystem;
use Wave8\Factotum\Cms\Contracts\Api\ContentFieldServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\ContentField\CreateContentFieldDto;
use Wave8\Factotum\Cms\Dtos\Api\ContentField\UpdateContentFieldDto;
use Wave8\Factotum\Cms\Models\ContentField;
use Wave8\Factotum\Cms\Models\ContentType;

#[AllowDynamicProperties]
class ContentFieldService implements ContentFieldServiceInterface
{
    public function __construct(public readonly ContentField $model)
    {
    }

    public function create(CreateContentFieldDto $data): ContentField
    {
        return $this->model::create($data->toArray());
    }

    public function update(ContentField $contentField, UpdateContentFieldDto $data): ContentField
    {
        $contentField->update($data->toArray());

        return $contentField;
    }

    public function createFieldForContentType(ContentType $contentType, CreateContentFieldDto $data): ContentField
    {
        return $contentType->contentFields()->create(
            $data->toArray()
        );
    }

    public function delete(ContentField $contentField): bool
    {
        return $contentField->delete();
    }
}
