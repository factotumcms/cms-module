<?php

namespace Wave8\Factotum\Cms\Models;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;
use Wave8\Factotum\Base\Enums\Locale;
use Wave8\Factotum\Cms\Policies\TermPolicy;
use Wave8\Factotum\Cms\Traits\HasTranslations;
use Wave8\Factotum\Cms\Traits\HasUrlAliases;

#[UsePolicy(TermPolicy::class)]
class Term extends Model
{
    use HasTranslations;
    use HasUrlAliases;
    use NodeTrait;
    use SoftDeletes;

    protected $fillable = [
        'taxonomy_id',
        'parent_id',
        'name',
        'slug',
        'description',
        'lang',
        'sort_order',
    ];

    protected $casts = [
        'lang' => Locale::class,
    ];

    protected $searchable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Scope the nested set tree per taxonomy.
     */
    protected function getScopeAttributes(): array
    {
        return ['taxonomy_id'];
    }

    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class);
    }

    public function contents(): MorphToMany
    {
        return $this->morphedByMany(Content::class, 'termable')->withTimestamps();
    }

    /**
     * Build the hierarchical URL path for this term.
     * Example: "tecnologia/intelligenza-artificiale"
     */
    public function buildHierarchicalPath(): string
    {
        $ancestors = $this->ancestors()->defaultOrder()->get();

        $segments = $ancestors->pluck('slug')->push($this->slug);

        return $segments->implode('/');
    }
}
