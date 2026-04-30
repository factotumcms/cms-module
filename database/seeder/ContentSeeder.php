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
use Wave8\Factotum\Cms\Enums\PageOperation;
use Wave8\Factotum\Cms\Resources\Models\Content\ContentSeoParamsResource;
use Wave8\Factotum\Cms\Resources\Models\Content\ContentSocialParamsResource;
use Wave8\Factotum\Cms\Services\Api\ContentService;
use Wave8\Factotum\Cms\Services\Api\ContentTypeService;
use Wave8\Factotum\Cms\Services\Api\TranslationService;

class ContentSeeder extends Seeder
{
    public function __construct(
        /** @var ContentService $contentService */
        private readonly ContentServiceInterface $contentService,

        /** @var ContentTypeService $contentTypeService */
        private readonly ContentTypeServiceInterface $contentTypeService,

        /** @var UserService $UserService */
        private readonly UserServiceInterface $userService,

        /** @var TranslationService $translationService */
        private readonly TranslationServiceInterface $translationService,
    ) {}

    public function run(): void
    {
        $this->createPages();
        $this->createNews();
    }

    public function createPages(): void
    {
        $contentIt = $this->contentService->createContentForContentType(
            contentType: $this->contentTypeService->getByType(ContentTypeEnum::PAGES),
            data: new CreateContentDto(
                status: ContentStatus::PUBLISHED,
                title: 'Home',
                editorType: ContentEditorType::BUILDER,
                content: '<h2>Home page contenuto</h2><br><p>Lorem ipsum è un testo segnaposto</p>',
                url: 'homepage',
                lang: Locale::IT,
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
                ),
                fields: [
                    'page_template' => 'basic',
                    'page_operation' => PageOperation::SHOW_CONTENT,
                ]
            )
        );

        $contentEn = $this->contentService->createContentForContentType(
            contentType: $this->contentTypeService->getByType(ContentTypeEnum::PAGES),
            data: new CreateContentDto(
                status: ContentStatus::PUBLISHED,
                title: 'Home',
                editorType: ContentEditorType::BUILDER,
                content: '<h2>Home page content</h2><br><p>Lorem ipsum is a placeholder text</p>',
                url: 'homepage',
                lang: Locale::EN,
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
                ),
                fields: [
                    'page_template' => 'basic',
                    'page_operation' => PageOperation::SHOW_CONTENT,
                ]
            )
        );

        $this->translationService->link(
            source: $contentIt,
            target: $contentEn,
            sourceLocale: Locale::IT,
            targetLocale: Locale::EN,
        );
    }

    private function createNews(): void
    {
        $newsIt = $this->contentService->createContentForContentType(
            contentType: $this->contentTypeService->getByType(ContentTypeEnum::NEWS),
            data: new CreateContentDto(
                status: ContentStatus::PUBLISHED,
                title: 'Titolo della news',
                editorType: ContentEditorType::BUILDER,
                content: '<h2>News contenuto</h2><br><p>Lorem ipsum è un testo segnaposto</p>',
                url: 'titolo-della-news',
                lang: Locale::IT,
                isHome: true,
                userId: $this->userService->getBy('email', config('factotum_base.admin_default.email'))->first()->id,
                seoParams: new ContentSeoParamsResource(
                    title: 'News contenuto',
                    description: 'News contenuto',
                    canonicalUrl: 'titolo-della-news',
                ),
                socialParams: new ContentSocialParamsResource(
                    fbTitle: 'Fb page content',
                    fbDescription: 'Fb page content'
                ),
                fields: [
                    'news_subtitle' => 'Sottotitolo della news',
                ]
            )
        );

        $newsEn = $this->contentService->createContentForContentType(
            contentType: $this->contentTypeService->getByType(ContentTypeEnum::NEWS),
            data: new CreateContentDto(
                status: ContentStatus::PUBLISHED,
                title: 'News title',
                editorType: ContentEditorType::BUILDER,
                content: '<h2>News content</h2><br><p>Lorem ipsum is a placeholder text</p>',
                url: 'news-title',
                lang: Locale::EN,
                isHome: true,
                userId: $this->userService->getBy('email', config('factotum_base.admin_default.email'))->first()->id,
                seoParams: new ContentSeoParamsResource(
                    title: 'News content',
                    description: 'News content',
                    canonicalUrl: 'news-title',
                ),
                socialParams: new ContentSocialParamsResource(
                    fbTitle: 'Fb page content',
                    fbDescription: 'Fb page content'
                ),
                fields: [
                    'news_subtitle' => 'News subtitle',
                ]
            )
        );

        $this->translationService->link(
            source: $newsIt,
            target: $newsEn,
            sourceLocale: Locale::IT,
            targetLocale: Locale::EN,
        );
    }
}
