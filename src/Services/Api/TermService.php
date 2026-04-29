<?php

namespace Wave8\Factotum\Cms\Services\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
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
        return Term::findOrFail($id);
    }

    public function createForTaxonomy(Taxonomy $taxonomy, CreateTermDto $data): Term
    {
        if ($data->parentId && ! $this->isValidParent($taxonomy, $data->parentId)) {
            throw new \InvalidArgumentException('Parent term does not belong to the same taxonomy.');
        }

        $attributes = $data->toArray();
        $attributes['taxonomy_id'] = $taxonomy->id;
        unset($attributes['parent_id']);

        $term = new Term($attributes);

        if ($data->parentId) {
            $parent = Term::findOrFail($data->parentId);
            $parent->appendNode($term);
        } else {
            $term->saveAsRoot();
        }

        return $term->fresh();
    }

    public function update(Term $term, UpdateTermDto $data): Term
    {
        $newParentId = $data->parentId ?? null;

        if ($newParentId && ! $this->isValidParent($term->taxonomy, $newParentId)) {
            throw new \InvalidArgumentException('Parent term does not belong to the same taxonomy.');
        }

        $updateData = $data->toArray();
        unset($updateData['parent_id']);

        $term->update($updateData);

        // Handle parent change via nestedset
        if ($data->parentId !== null) {
            if ($newParentId) {
                $newParent = Term::findOrFail($newParentId);
                $term->appendToNode($newParent)->save();
            } else {
                $term->makeRoot()->save();
            }
        }

        return $term->fresh();
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
     * Get the full tree for a taxonomy.
     */
    public function getTree(Taxonomy $taxonomy): Collection
    {
        return Term::scoped(['taxonomy_id' => $taxonomy->id])
            ->defaultOrder()
            ->get()
            ->toTree();
    }

    /**
     * Validate that the parent term belongs to the given taxonomy.
     */
    private function isValidParent(Taxonomy $taxonomy, int $parentId): bool
    {
        return $taxonomy->terms()->where('id', $parentId)->exists();
    }
}
