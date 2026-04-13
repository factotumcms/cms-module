<?php

namespace Wave8\Factotum\Cms\Contracts\Api;

use Wave8\Factotum\Cms\Dtos\Api\ContentField\CreateContentFieldDto;
use Wave8\Factotum\Cms\Dtos\Api\ContentField\UpdateContentFieldDto;
use Wave8\Factotum\Cms\Models\ContentField;
use Wave8\Factotum\Cms\Models\ContentType;

interface ContentFieldServiceInterface
{
    public function create(CreateContentFieldDto $data): ContentField;

    public function update(ContentField $contentField, UpdateContentFieldDto $data): ContentField;

    public function createFieldForContentType(ContentType $contentType, CreateContentFieldDto $data): ContentField;
}
