<?php

use Wave8\Factotum\Cms\Dtos\Api\Content\CreateContentDto;
use Wave8\Factotum\Cms\Dtos\Api\Content\UpdateContentDto;
use Wave8\Factotum\Cms\Dtos\Api\ContentField\CreateContentFieldDto;
use Wave8\Factotum\Cms\Dtos\Api\ContentField\UpdateContentFieldDto;
use Wave8\Factotum\Cms\Dtos\Api\ContentType\CreateContentTypeDto;
use Wave8\Factotum\Cms\Dtos\Api\ContentType\UpdateContentTypeDto;
use Wave8\Factotum\Cms\Dtos\Api\Taxonomy\CreateTaxonomyDto;
use Wave8\Factotum\Cms\Dtos\Api\Taxonomy\UpdateTaxonomyDto;
use Wave8\Factotum\Cms\Dtos\Api\Term\CreateTermDto;
use Wave8\Factotum\Cms\Dtos\Api\Term\UpdateTermDto;
use Wave8\Factotum\Cms\Resources\Api\ContentFieldResource;
use Wave8\Factotum\Cms\Resources\Api\ContentResource;
use Wave8\Factotum\Cms\Resources\Api\ContentTypeResource;
use Wave8\Factotum\Cms\Resources\Api\TaxonomyResource;
use Wave8\Factotum\Cms\Resources\Api\TermResource;

return [
    // Dto Bindings
    CreateContentDto::class => CreateContentDto::class,
    CreateContentFieldDto::class => CreateContentFieldDto::class,
    CreateContentTypeDto::class => CreateContentTypeDto::class,
    UpdateContentTypeDto::class => UpdateContentTypeDto::class,
    UpdateContentFieldDto::class => UpdateContentFieldDto::class,
    UpdateContentDto::class => UpdateContentDto::class,
    CreateTaxonomyDto::class => CreateTaxonomyDto::class,
    UpdateTaxonomyDto::class => UpdateTaxonomyDto::class,
    CreateTermDto::class => CreateTermDto::class,
    UpdateTermDto::class => UpdateTermDto::class,

    // Resources Bindings
    ContentTypeResource::class => ContentTypeResource::class,
    ContentFieldResource::class => ContentFieldResource::class,
    ContentResource::class => ContentResource::class,
    TaxonomyResource::class => TaxonomyResource::class,
    TermResource::class => TermResource::class,
];
