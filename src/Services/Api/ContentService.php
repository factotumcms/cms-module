<?php

namespace Wave8\Factotum\Cms\Services\Api;

use Wave8\Factotum\Cms\Contracts\Api\ContentServiceInterface;
use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\Content\CreateContentDto;
use Wave8\Factotum\Cms\Models\Content;
use Wave8\Factotum\Cms\Models\ContentType;

class ContentService implements ContentServiceInterface
{
    /** @var ContentTypeService */
    private ContentTypeServiceInterface $contentTypeService;

    public function __construct(public readonly Content $model)
    {
        $this->contentTypeService = app(ContentTypeServiceInterface::class);
    }

    public function single(int $id): Content
    {
        return $this->model::findOrFail($id);
    }

    public function create(CreateContentDto $data): Content
    {
        $ct = $this->contentTypeService->single($data->contentTypeId);

        return $this->createForContentType($ct, $data->toArray());
    }

    public function createForContentType(ContentType $contentType, array $data): Content
    {
        return $contentType->contents()->create($data);
    }
}
