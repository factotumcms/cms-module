<?php

namespace Wave8\Factotum\Cms\Database\Seeder;

use Illuminate\Database\Seeder;
use Wave8\Factotum\Base\Enums\Locale;
use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Contracts\Api\TaxonomyServiceInterface;
use Wave8\Factotum\Cms\Contracts\Api\TermServiceInterface;
use Wave8\Factotum\Cms\Contracts\Api\TranslationServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\Taxonomy\CreateTaxonomyDto;
use Wave8\Factotum\Cms\Dtos\Api\Term\CreateTermDto;
use Wave8\Factotum\Cms\Enums\BaseContentType;
use Wave8\Factotum\Cms\Models\Content;
use Wave8\Factotum\Cms\Services\Api\ContentTypeService;
use Wave8\Factotum\Cms\Services\Api\TaxonomyService;
use Wave8\Factotum\Cms\Services\Api\TermService;
use Wave8\Factotum\Cms\Services\Api\TranslationService;

class TaxonomySeeder extends Seeder
{
    public function __construct(
        /** @var TaxonomyService $taxonomyService */
        private readonly TaxonomyServiceInterface $taxonomyService,

        /** @var TermService $termService */
        private readonly TermServiceInterface $termService,

        /** @var ContentTypeService $contentTypeService */
        private readonly ContentTypeServiceInterface $contentTypeService,

        /** @var TranslationService $translationService */
        private readonly TranslationServiceInterface $translationService,
    ) {}

    public function run(): void
    {
        $taxonomy = $this->taxonomyService->create(
            new CreateTaxonomyDto(
                name: 'page-category',
                label: 'Categorie',
                isHierarchical: true,
            )
        );

        $ct = $this->contentTypeService->getByType(BaseContentType::PAGES);
        $this->taxonomyService->attachToContentType($taxonomy, $ct, isRequired: false, allowMultiple: true);

        $termIt = $this->termService->createForTaxonomy(
            taxonomy: $taxonomy,
            data: new CreateTermDto(
                name: 'Categoria test',
                lang: Locale::IT,
            )
        );

        $contents = Content::where('lang', Locale::IT->value)->get();
        foreach ($contents as $content) {
            $this->termService->syncTermsToModel($content, [$termIt->id]);
        }

        $termEn = $this->termService->createForTaxonomy(
            taxonomy: $taxonomy,
            data: new CreateTermDto(
                name: 'Test category',
                lang: Locale::EN,
            )
        );

        $contents = Content::where('lang', Locale::EN->value)->get();
        foreach ($contents as $content) {
            $this->termService->syncTermsToModel($content, [$termEn->id]);
        }

        $this->translationService->link(
            source: $termIt,
            target: $termEn,
            sourceLocale: Locale::IT,
            targetLocale: Locale::EN,
        );
    }
}
