<?php

namespace Wave8\Factotum\Cms\Database\Seeder;

use Illuminate\Database\Seeder;
use Wave8\Factotum\Cms\Contracts\Api\ContentFieldServiceInterface;
use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\ContentField\CreateContentFieldDto;
use Wave8\Factotum\Cms\Enums\BaseContentType as BaseContentTypeEnum;
use Wave8\Factotum\Cms\Enums\ContentFieldType;
use Wave8\Factotum\Cms\Models\ContentType;
use Wave8\Factotum\Cms\Resources\Models\ContentField\CfConfigResource;
use Wave8\Factotum\Cms\Resources\Models\ContentField\CfImageUploadConfigResource;
use Wave8\Factotum\Cms\Resources\Models\ContentField\CfSelectConfigResource;
use Wave8\Factotum\Cms\Services\Api\ContentFieldService;
use Wave8\Factotum\Cms\Services\Api\ContentTypeService;

class ContentFieldSeeder extends Seeder
{
    private readonly ContentTypeServiceInterface $contentTypeService;

    private readonly ContentFieldService $contentFieldService;

    public function run(): void
    {
        /** @var ContentTypeService $service */
        $this->contentTypeService = app(ContentTypeServiceInterface::class);

        /** @var ContentFieldService $service */
        $this->contentFieldService = app(ContentFieldServiceInterface::class);

        $pageContentType = $this->contentTypeService->getByType(BaseContentTypeEnum::PAGES);
        $newsContentType = $this->contentTypeService->getByType(BaseContentTypeEnum::NEWS);

        $this->createPageContentFields($pageContentType);
        $this->createPageContentListFields($pageContentType);
        $this->createPageContentLinkFields($pageContentType);
        $this->createNewsContentFields($newsContentType);
    }

    private function createPageContentFields(ContentType $contentType): void
    {
        $this->contentFieldService->createFieldForContentType($contentType,
            new CreateContentFieldDto(
                name: 'page_template',
                label: 'Page Template',
                type: ContentFieldType::SELECT,
                configs: new CfConfigResource(
                    cfParams: new CfSelectConfigResource(
                        options: [
                            ['value' => 'home',           'label' => 'Home Page Template'],
                            ['value' => 'basic',          'label' => 'Basic Page Template'],
                            ['value' => 'content_list',   'label' => 'Content List Page Template'],

                            ['value' => 'contact_us',     'label' => 'Contact Us Page Template'],
                            ['value' => 'thankyou_page',  'label' => 'Thank You Page Template'],

                            ['value' => 'about_us',       'label' => 'About Us Page Template'],
                            ['value' => 'privacy_policy', 'label' => 'Privacy Policy Page Template'],
                            ['value' => 'cookie_policy',  'label' => 'Cookie Policy Page Template'],
                        ],
                    ),
                    visibilityRules: [
                        [
                            ['contentField' => 'page_operation', 'operator' => '!=', 'value' => 'link'],
                            ['contentField' => 'page_operation', 'operator' => '!=', 'value' => 'action'],
                        ],
                    ],
                    mandatory: true
                )
            )
        );

        $this->contentFieldService->createFieldForContentType($contentType,
            new CreateContentFieldDto(
                name: 'page_operation',
                label: 'Page Operation',
                type: ContentFieldType::SELECT,
                configs: new CfConfigResource(
                    cfParams: new CfSelectConfigResource(
                        options: [
                            ['value' => 'show_content', 'label' => 'Show Page Content'],
                            ['value' => 'content_list', 'label' => 'Show Content List'],
                            ['value' => 'link',         'label' => 'Link'],
                            ['value' => 'action',       'label' => 'Action'],
                        ]),
                    mandatory: true
                ),
            )
        );

        $this->contentFieldService->createFieldForContentType($contentType,
            new CreateContentFieldDto(
                name: 'action',
                label: 'Action',
                type: ContentFieldType::TEXT,
                configs: new CfConfigResource(
                    visibilityRules: [
                        [
                            ['contentField' => 'page_operation', 'operator' => '=', 'value' => 'action'],
                        ],
                    ],
                    mandatory: true
                )
            )
        );

        $this->contentFieldService->createFieldForContentType($contentType,
            new CreateContentFieldDto(
                name: 'page_cover',
                label: 'Page Cover',
                type: ContentFieldType::IMAGE_UPLOAD,
                configs: new CfConfigResource(
                    cfParams: new CfImageUploadConfigResource(
                        minWidthSize: 100,
                        minHeightSize: 100,
                        maxFileSize: 2,
                        imageOperation: 'fit',
                        resizes: [
                            [
                                'w' => 370, 'h' => 210,
                            ],
                        ],
                    ),
                    mandatory: false
                ),
            )
        );
    }

    private function createPageContentListFields(ContentType $contentType): void
    {
        $contentListRules = [
            [
                ['contentField' => 'page_operation', 'operator' => '=', 'value' => 'content_list'],
            ],
        ];
        $this->contentFieldService->createFieldForContentType($contentType,
            new CreateContentFieldDto(
                name: 'content_type_to_list',
                label: 'Content Type To List',
                type: ContentFieldType::SELECT,
                configs: new CfConfigResource(
                    visibilityRules: $contentListRules,
                    mandatory: true,
                )
            )
        );

        $this->contentFieldService->createFieldForContentType($contentType,
            new CreateContentFieldDto(
                name: 'content_list_pagination',
                label: 'Content List Pagination',
                type: ContentFieldType::NUMBER,
                configs: new CfConfigResource(
                    visibilityRules: $contentListRules,
                )
            )
        );

        $this->contentFieldService->createFieldForContentType($contentType,
            new CreateContentFieldDto(
                name: 'content_list_order',
                label: 'Content List Order',
                type: ContentFieldType::SELECT,
                configs: new CfConfigResource(
                    cfParams: new CfSelectConfigResource(
                        options: [
                            ['value' => 'contents.id-asc',          'label' => 'BY ID ASC'],
                            ['value' => 'contents.id-desc',         'label' => 'BY ID DESC'],
                            ['value' => 'contents.created_at-asc',  'label' => 'BY DATA CREATION ASC'],
                            ['value' => 'contents.created_at-desc', 'label' => 'BY DATA CREATION DESC'],
                            ['value' => 'contents.order_no-asc',    'label' => 'BY ORDER No. ASC'],
                            ['value' => 'contents.order_no-desc',   'label' => 'BY ORDER No. DESC'],
                            ['value' => 'contents.title-asc',       'label' => 'BY TITLE ASC'],
                            ['value' => 'contents.title-desc',      'label' => 'BY TITLE DESC'],
                        ]
                    ),
                    visibilityRules: $contentListRules,
                )
            )
        );
    }

    private function createPageContentLinkFields(ContentType $contentType): void
    {
        $linkRules = [
            [
                ['contentField' => 'page_operation', 'operator' => '=', 'value' => 'link'],
            ],
        ];

        $this->contentFieldService->createFieldForContentType($contentType,
            new CreateContentFieldDto(
                name: 'link',
                label: 'Link',
                type: ContentFieldType::URL,
                configs: new CfConfigResource(
                    visibilityRules: $linkRules,
                    mandatory: true
                ),
            )
        );

        $this->contentFieldService->createFieldForContentType($contentType,
            new CreateContentFieldDto(
                name: 'link_title',
                label: 'Link Title',
                type: ContentFieldType::TEXT,
                configs: new CfConfigResource(
                    visibilityRules: $linkRules
                ),
            )
        );

        $this->contentFieldService->createFieldForContentType($contentType,
            new CreateContentFieldDto(
                name: 'link_open_in',
                label: 'Link Open In',
                type: ContentFieldType::SELECT,
                configs: new CfConfigResource(
                    cfParams: new CfSelectConfigResource(
                        options: [
                            ['value' => '_self',  'label' => 'Same Page'],
                            ['value' => '_blank', 'label' => 'New Page'],
                        ]
                    ),
                    visibilityRules: $linkRules
                ),
            )
        );
    }

    private function createNewsContentFields(ContentType $contentType): void
    {
        $this->contentFieldService->createFieldForContentType($contentType,
            new CreateContentFieldDto(
                name: 'news_subtitle',
                label: 'News Subtitle',
                type: ContentFieldType::TEXT
            )
        );
    }
}
