<?php

namespace Wave8\Factotum\Cms\Database\Seeder;

use Illuminate\Database\Seeder;
use Wave8\Factotum\Base\Contracts\Api\UserServiceInterface;
use Wave8\Factotum\Base\Enums\Locale;
use Wave8\Factotum\Base\Services\Api\UserService;
use Wave8\Factotum\Cms\Contracts\Api\ContentServiceInterface;
use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Contracts\Api\TranslationServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\Content\CreateContentDto;
use Wave8\Factotum\Cms\Enums\BaseContentType as ContentTypeEnum;
use Wave8\Factotum\Cms\Enums\ContentEditorType;
use Wave8\Factotum\Cms\Enums\ContentStatus;
use Wave8\Factotum\Cms\Resources\Models\Content\ContentSeoParamsResource;
use Wave8\Factotum\Cms\Resources\Models\Content\ContentSocialParamsResource;
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
        $contentIt = $this->contentService->createContentForContentType(
            contentType: $this->contentTypeService->getByType(ContentTypeEnum::PAGES),
            data: new CreateContentDto(
                status: ContentStatus::PUBLISHED,
                title: 'Home',
                editorType: ContentEditorType::BUILDER,
                content: 'Home page contenuto',
                url: 'homepage',
                lang: Locale::from('it'),
                isHome: true,
                userId: $this->userService->getBy('email', config('factotum_base.admin_default.email'))->first()->id,
                seoParams: new ContentSeoParamsResource(
                    title: 'Home page content',
                    description: 'Home page content',
                    canonicalUrl: 'homepage',
                ),
                socialParams: new ContentSocialParamsResource(
                    fbTitle: 'Fb page content',
                    fbDescription: 'Fb page content'
                )
            )
        );

        $contentEn = $this->contentService->createContentForContentType(
            contentType: $this->contentTypeService->getByType(ContentTypeEnum::PAGES),
            data: new CreateContentDto(
                status: ContentStatus::PUBLISHED,
                title: 'Home',
                editorType: ContentEditorType::BUILDER,
                content: 'Home page content',
                url: 'homepage',
                lang: Locale::from('en'),
                isHome: true,
                userId: $this->userService->getBy('email', config('factotum_base.admin_default.email'))->first()->id,
                seoParams: new ContentSeoParamsResource(
                    title: 'Home page content',
                    description: 'Home page content',
                    canonicalUrl: 'homepage',
                ),
                socialParams: new ContentSocialParamsResource(
                    fbTitle: 'Fb page content',
                    fbDescription: 'Fb page content'
                )
            )
        );

        $translationService = app(TranslationServiceInterface::class);

        $translationService->link(
            source: $contentIt,
            target: $contentEn,
            sourceLocale: Locale::IT,
            targetLocale: Locale::EN,
        );
    }
}
