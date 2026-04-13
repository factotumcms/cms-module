<?php

namespace Wave8\Factotum\Cms\Contracts\Api;

use Wave8\Factotum\Cms\Dtos\Api\ContentField\CreateContentFieldDto;
use Wave8\Factotum\Cms\Models\ContentField;
use Wave8\Factotum\Cms\Models\ContentType;

interface ContentFieldServiceInterface
{
    public function single(int $id): ContentField;

    public function create(CreateContentFieldDto $data): ContentField;

    public function createFieldForContentType(ContentType $contentType, CreateContentFieldDto $data): ContentField;
}
