<?php

namespace Wave8\Factotum\Cms\Http\Controllers\Api;

use Wave8\Factotum\Cms\Resources\Api\ContentFieldResource;

final readonly class ContentFieldController
{
    public string $contentFieldResource;

    public function __construct(
    ) {
        $this->contentFieldResource = config('data_transfer.'.ContentFieldResource::class);
    }
}
