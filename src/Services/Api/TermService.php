<?php

namespace Wave8\Factotum\Cms\Services\Api;

use Illuminate\Database\Eloquent\Model;
use Wave8\Factotum\Cms\Contracts\Api\TermServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\Term\CreateTermDto;
use Wave8\Factotum\Cms\Dtos\Api\Term\UpdateTermDto;
use Wave8\Factotum\Cms\Models\Taxonomy;
use Wave8\Factotum\Cms\Models\Term;

readonly class TermService implements TermServiceInterface
{
    public function __construct(public Term $model) {}

    public function single(int $id): Term
    {
        return $this->model::findOrFail($id);
    }

    public function createForTaxonomy(Taxonomy $taxonomy, CreateTermDto $data): Term
    {
        if ($data->parentId && ! $this->isValidParent($taxonomy, $data->parentId)) {
            throw new \InvalidArgumentException('Parent term does not belong to the same taxonomy.');
        }

        return $taxonomy->terms()->create($data->toArray());
    }

    public function update(Term $term, UpdateTermDto $data): Term
    {
        if ($data->parentId && ! $this->isValidParent($term->taxonomy, $data->parentId)) {
            throw new \InvalidArgumentException('Parent term does not belong to the same taxonomy.');
        }

        $term->update($data->toArray());

        return $term;
    }

    public function delete(Term $term): bool
    {
        return $term->delete();
    }

    public function syncTermsToModel(Model $model, array $termIds): void
    {
        $model->terms()->sync($termIds);
    }

    public function detachTermFromModel(Model $model, Term $term): void
    {
        $model->terms()->detach($term->id);
    }

    /**
     * Validate that the parent term belongs to the given taxonomy.
     */
    private function isValidParent(Taxonomy $taxonomy, int $parentId): bool
    {
        return $taxonomy->terms()->where('id', $parentId)->exists();
    }
}
