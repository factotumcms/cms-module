<?php

namespace Wave8\Factotum\Cms\Contracts\Api;

use Wave8\Factotum\Cms\Dtos\Api\Content\CreateContentDto;
use Wave8\Factotum\Cms\Models\Content;

interface ContentServiceInterface
{
    public function single(int $id): Content;

    public function create(CreateContentDto $data): Content;
}
