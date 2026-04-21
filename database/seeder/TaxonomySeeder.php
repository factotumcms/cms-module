<?php

namespace Wave8\Factotum\Cms\Database\Seeder;

use Illuminate\Database\Seeder;
use Wave8\Factotum\Base\Enums\Locale;
use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Contracts\Api\TaxonomyServiceInterface;
use Wave8\Factotum\Cms\Contracts\Api\TermServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\Taxonomy\CreateTaxonomyDto;
use Wave8\Factotum\Cms\Dtos\Api\Term\CreateTermDto;
use Wave8\Factotum\Cms\Enums\BaseContentType;
use Wave8\Factotum\Cms\Models\Content;
use Wave8\Factotum\Cms\Services\Api\ContentTypeService;
use Wave8\Factotum\Cms\Services\Api\TaxonomyService;
use Wave8\Factotum\Cms\Services\Api\TermService;

class TaxonomySeeder extends Seeder
{
    public function __construct(
        /** @var TaxonomyService $taxonomyService */
        private readonly TaxonomyServiceInterface $taxonomyService,

        /** @var TermService $termService */
        private readonly TermServiceInterface $termService,

        /** @var ContentTypeService $contentTypeService */
        private readonly ContentTypeServiceInterface $contentTypeService,
    ) {}

    public function run(): void
    {
        // Create the "Categorie" taxonomy (hierarchical)
        $taxonomy = $this->taxonomyService->create(
            new CreateTaxonomyDto(
                name: 'categories',
                label: 'Categorie',
                isHierarchical: true,
            )
        );

        // Associate it with both content types
        $pages = $this->contentTypeService->getByType(BaseContentType::PAGES);
        $news = $this->contentTypeService->getByType(BaseContentType::NEWS);

        $this->taxonomyService->attachToContentType($taxonomy, $pages, isRequired: false, allowMultiple: true);
        $this->taxonomyService->attachToContentType($taxonomy, $news, isRequired: false, allowMultiple: true);

        // Create the "Categoria test" term for each locale
        foreach (Locale::getValues() as $locale) {
            $term = $this->termService->createForTaxonomy(
                taxonomy: $taxonomy,
                data: new CreateTermDto(
                    name: 'Categoria test',
                    lang: Locale::from($locale),
                )
            );

            // Attach the term to all existing contents in this locale
            $contents = Content::where('lang', $locale)->get();
            foreach ($contents as $content) {
                $this->termService->syncTermsToModel($content, [$term->id]);
            }
        }
    }
}
