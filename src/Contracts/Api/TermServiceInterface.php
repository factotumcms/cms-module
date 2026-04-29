<?php

namespace Wave8\Factotum\Cms\Contracts\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Wave8\Factotum\Cms\Dtos\Api\Term\CreateTermDto;
use Wave8\Factotum\Cms\Dtos\Api\Term\UpdateTermDto;
use Wave8\Factotum\Cms\Models\Taxonomy;
use Wave8\Factotum\Cms\Models\Term;

interface TermServiceInterface
{
    public function single(int $id): Term;

    public function createForTaxonomy(Taxonomy $taxonomy, CreateTermDto $data): Term;

    public function update(Term $term, UpdateTermDto $data): Term;

    public function delete(Term $term): bool;

    public function syncTermsToModel(Model $model, array $termIds): void;

    public function detachTermFromModel(Model $model, Term $term): void;

    public function getTree(Taxonomy $taxonomy): Collection;
}
