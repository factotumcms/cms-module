<?php

namespace Wave8\Factotum\Cms\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Wave8\Factotum\Cms\Models\UrlAlias;

trait HasUrlAliases
{
    public function urlAliases(): MorphMany
    {
        return $this->morphMany(UrlAlias::class, 'routable');
    }

    public function canonicalUrl(): ?UrlAlias
    {
        return $this->urlAliases()->canonical()->first();
    }

    public function getCanonicalUri(): ?string
    {
        return $this->canonicalUrl()?->uri;
    }
}

