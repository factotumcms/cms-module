<?php

namespace Wave8\Factotum\Cms\Database\Seeder;

use Illuminate\Database\Seeder;
use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\ContentType\CreateContentTypeDto;
use Wave8\Factotum\Cms\Enums\ContentType;
use Wave8\Factotum\Cms\Services\Api\ContentTypeService;

class ContentTypeSeeder extends Seeder
{
    public function run(): void
    {
        /** @var ContentTypeService $service */
        $service = app(ContentTypeServiceInterface::class);

        $service->create(
            new CreateContentTypeDto(
                type: ContentType::PAGE,
                label: 'Pagine',
                editable: false,
                icon: 'content',
                sitemap: true,
                visible: true,
                hierarchical: true,
            )
        );

        $service->create(
            new CreateContentTypeDto(
                type: ContentType::NEWS,
                label: 'News',
                editable: true,
                icon: 'news',
                sitemap: true,
                visible: true,
                hierarchical: false,
            )
        );
    }
}
