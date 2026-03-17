<?php

namespace Wave8\Factotum\Cms\Contracts\Api;

use Wave8\Factotum\Cms\Models\ContentType;

interface ContentTypeServiceInterface
{
    public function generateDynamicTableAndModel(ContentType $contentType): void;
}
