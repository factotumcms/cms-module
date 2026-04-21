<?php

namespace Wave8\Factotum\Cms\Services\Api;

use Wave8\Factotum\Cms\Contracts\Api\TaxonomyServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\Taxonomy\CreateTaxonomyDto;
use Wave8\Factotum\Cms\Dtos\Api\Taxonomy\UpdateTaxonomyDto;
use Wave8\Factotum\Cms\Models\ContentType;
use Wave8\Factotum\Cms\Models\Taxonomy;

readonly class TaxonomyService implements TaxonomyServiceInterface
{
    public function __construct(public Taxonomy $model) {}

    public function single(int $id): Taxonomy
    {
        return $this->model::findOrFail($id);
    }

    public function create(CreateTaxonomyDto $data): Taxonomy
    {
        return $this->model::create($data->toArray());
    }

    public function update(Taxonomy $taxonomy, UpdateTaxonomyDto $data): Taxonomy
    {
        $taxonomy->update($data->toArray());

        return $taxonomy;
    }

    public function delete(Taxonomy $taxonomy): bool
    {
        return $taxonomy->delete();
    }

    public function attachToContentType(Taxonomy $taxonomy, ContentType $contentType, bool $isRequired = false, bool $allowMultiple = true): void
    {
        $taxonomy->contentTypes()->syncWithoutDetaching([
            $contentType->id => [
                'is_required' => $isRequired,
                'allow_multiple' => $allowMultiple,
            ],
        ]);
    }

    public function detachFromContentType(Taxonomy $taxonomy, ContentType $contentType): void
    {
        $taxonomy->contentTypes()->detach($contentType->id);
    }
}
