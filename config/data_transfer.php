<?php

use Wave8\Factotum\Cms\Resources\Api\ContentResource;
use Wave8\Factotum\Cms\Resources\Api\ContentTypeResource;

return [
    // Dto Bindings

    // Resources Bindings
    ContentTypeResource::class => ContentTypeResource::class,
    ContentResource::class => ContentResource::class,
];
