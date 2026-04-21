<?php

namespace Wave8\Factotum\Cms\Models;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wave8\Factotum\Cms\Policies\TaxonomyPolicy;

#[UsePolicy(TaxonomyPolicy::class)]
class Taxonomy extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'label',
        'is_hierarchical',
        'sort_order',
    ];

    protected $casts = [
        'is_hierarchical' => 'boolean',
    ];

    protected $searchable = [
        'name',
        'label',
    ];

    public function terms(): HasMany
    {
        return $this->hasMany(Term::class);
    }

    public function contentTypes(): BelongsToMany
    {
        return $this->belongsToMany(ContentType::class, 'content_type_taxonomy')
            ->withPivot(['is_required', 'allow_multiple'])
            ->withTimestamps();
    }
}
