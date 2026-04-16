<?php

use Wave8\Factotum\Cms\Dtos\Api\Content\CreateContentDto;
use Wave8\Factotum\Cms\Dtos\Api\Content\UpdateContentDto;
use Wave8\Factotum\Cms\Dtos\Api\ContentField\CreateContentFieldDto;
use Wave8\Factotum\Cms\Dtos\Api\ContentField\UpdateContentFieldDto;
use Wave8\Factotum\Cms\Dtos\Api\ContentType\CreateContentTypeDto;
use Wave8\Factotum\Cms\Dtos\Api\ContentType\UpdateContentTypeDto;
use Wave8\Factotum\Cms\Resources\Api\ContentFieldResource;
use Wave8\Factotum\Cms\Resources\Api\ContentResource;
use Wave8\Factotum\Cms\Resources\Api\ContentTypeResource;

return [
    // Dto Bindings
    CreateContentDto::class => CreateContentDto::class,
    CreateContentFieldDto::class => CreateContentFieldDto::class,
    CreateContentTypeDto::class => CreateContentTypeDto::class,
    UpdateContentTypeDto::class => UpdateContentTypeDto::class,
    UpdateContentFieldDto::class => UpdateContentFieldDto::class,
    UpdateContentDto::class => UpdateContentDto::class,

    // Resources Bindings
    ContentTypeResource::class => ContentTypeResource::class,
    ContentFieldResource::class => ContentFieldResource::class,
    ContentResource::class => ContentResource::class,
];
