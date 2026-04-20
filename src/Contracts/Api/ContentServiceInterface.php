<?php

namespace Wave8\Factotum\Cms\Contracts\Api;

use Wave8\Factotum\Cms\Dtos\Api\Content\CreateContentDto;
use Wave8\Factotum\Cms\Dtos\Api\Content\UpdateContentDto;
use Wave8\Factotum\Cms\Models\Content;
use Wave8\Factotum\Cms\Models\ContentType;

interface ContentServiceInterface
{
    public function single(int $id): Content;

    public function createContentForContentType(ContentType $contentType, CreateContentDto $data): Content;

    public function update(Content $content, UpdateContentDto $data): Content;

    public function delete(Content $content): bool;

    public function getDynamicFields(Content $content): array;
}
