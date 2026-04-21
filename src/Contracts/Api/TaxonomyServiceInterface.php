<?php

namespace Wave8\Factotum\Cms\Contracts\Api;

use Wave8\Factotum\Cms\Dtos\Api\Taxonomy\CreateTaxonomyDto;
use Wave8\Factotum\Cms\Dtos\Api\Taxonomy\UpdateTaxonomyDto;
use Wave8\Factotum\Cms\Models\ContentType;
use Wave8\Factotum\Cms\Models\Taxonomy;

interface TaxonomyServiceInterface
{
    public function single(int $id): Taxonomy;

    public function create(CreateTaxonomyDto $data): Taxonomy;

    public function update(Taxonomy $taxonomy, UpdateTaxonomyDto $data): Taxonomy;

    public function delete(Taxonomy $taxonomy): bool;

    public function attachToContentType(Taxonomy $taxonomy, ContentType $contentType, bool $isRequired, bool $allowMultiple): void;

    public function detachFromContentType(Taxonomy $taxonomy, ContentType $contentType): void;
}
