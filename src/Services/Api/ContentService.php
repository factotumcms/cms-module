<?php

namespace Wave8\Factotum\Cms\Services\Api;

use Wave8\Factotum\Cms\Contracts\Api\ContentServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\Content\CreateContentDto;
use Wave8\Factotum\Cms\Dtos\Api\Content\UpdateContentDto;
use Wave8\Factotum\Cms\Models\Content;
use Wave8\Factotum\Cms\Models\ContentType;

readonly class ContentService implements ContentServiceInterface
{
    public function __construct(public Content $model) {}

    public function single(int $id): Content
    {
        return $this->model::findOrFail($id);
    }

    public function createContentForContentType(ContentType $contentType, CreateContentDto $data): Content
    {
        return $contentType->contents()->create($data->toArray());
    }

    public function update(Content $content, UpdateContentDto $data): Content
    {
        $content->update($data->toArray());

        return $content;
    }

    public function delete(Content $content): bool
    {
        return $content->delete();
    }
}
