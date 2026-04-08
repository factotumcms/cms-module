<?php

namespace Wave8\Factotum\Cms\Contracts\Api;

use Wave8\Factotum\Cms\Dtos\Api\ContentField\CreateContentFieldDto;
use Wave8\Factotum\Cms\Dtos\Api\ContentType\CreateContentTypeDto;
use Wave8\Factotum\Cms\Enums\BaseContentType as ContentTypeEnum;
use Wave8\Factotum\Cms\Models\ContentField;
use Wave8\Factotum\Cms\Models\ContentType;

interface ContentTypeServiceInterface
{
    public function single(int $id): ContentType;

    public function create(CreateContentTypeDto $data): ContentType;

    public function getByType(ContentTypeEnum|string $type): ContentType;

    public function createFieldForContentType(ContentType $contentType, CreateContentFieldDto $data): ContentField;
}
