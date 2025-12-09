<?php

namespace Wave8\Factotum\Cms\Database\Seeder;

use Illuminate\Database\Seeder;
use Wave8\Factotum\Base\Contracts\Api\UserServiceInterface;
use Wave8\Factotum\Base\Enums\Locale;
use Wave8\Factotum\Base\Services\Api\UserService;
use Wave8\Factotum\Cms\Contracts\Api\ContentServiceInterface;
use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\Content\CreateContentDto;
use Wave8\Factotum\Cms\Enums\ContentEditorType;
use Wave8\Factotum\Cms\Enums\ContentStatus;
use Wave8\Factotum\Cms\Enums\ContentType as ContentTypeEnum;
use Wave8\Factotum\Cms\Services\Api\ContentService;
use Wave8\Factotum\Cms\Services\Api\ContentTypeService;

class ContentSeeder extends Seeder
{
    public function __construct(
        /** @var ContentService $contentService */
        private readonly ContentServiceInterface $contentService,

        /** @var ContentTypeService $contentTypeService */
        private readonly ContentTypeServiceInterface $contentTypeService,

        /** @var UserService UserService */
        private readonly UserServiceInterface $userService
    ) {}

    public function run(): void
    {
        foreach (Locale::getValues() as $locale) {
            $this->contentService->create(
                new CreateContentDto(
                    contentTypeId: $this->contentTypeService->getByType(ContentTypeEnum::PAGE)->id,
                    status: ContentStatus::PUBLISHED,
                    title: 'Home',
                    editorType: ContentEditorType::BUILDER,
                    content: 'Home page content',
                    url: 'homepage',
                    lang: Locale::from($locale),
                    isHome: true,
                    userId: $this->userService->getBy('email', config('factotum_base.admin_default.email'))->first()->id,
                )
            );
        }
    }
}
