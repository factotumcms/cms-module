<?php

namespace Wave8\Factotum\Cms\Database\Seeder;

use Illuminate\Database\Seeder;
use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\ContentField\CreateContentFieldDto;
use Wave8\Factotum\Cms\Enums\ContentType;
use Wave8\Factotum\Cms\Services\Api\ContentTypeService;

class ContentFieldSeeder extends Seeder
{
    public function run(): void
    {
        /** @var ContentTypeService $service */
        $service = app(ContentTypeServiceInterface::class);

        $service->createFieldForContentType(ContentType::PAGE,
            new CreateContentFieldDto(
                name: 'page_template',
                label: 'Page Template',
                type: 'select',
                mandatory: true,
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
                visibilityRules: [
                    [
                        ['contentField' => 'page_operation', 'operator' => '!=', 'value' => 'link'],
                        ['contentField' => 'page_operation', 'operator' => '!=', 'value' => 'action'],
                    ],
                ]
            ));
    }
}
