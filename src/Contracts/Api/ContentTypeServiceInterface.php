<?php

namespace Wave8\Factotum\Cms\Contracts\Api;

use Wave8\Factotum\Cms\Dtos\Api\ContentType\CreateContentTypeDto;
use Wave8\Factotum\Cms\Dtos\Api\ContentType\UpdateContentTypeDto;
use Wave8\Factotum\Cms\Enums\BaseContentType as ContentTypeEnum;
use Wave8\Factotum\Cms\Models\ContentType;

interface ContentTypeServiceInterface
{
    public function create(CreateContentTypeDto $data): ContentType;

    public function update(ContentType $contentType, UpdateContentTypeDto $data): ContentType;

    public function getByType(ContentTypeEnum|string $type): ContentType;

    public function delete(ContentType $contentType): bool;
}
