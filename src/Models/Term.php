<?php

namespace Wave8\Factotum\Cms\Models;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wave8\Factotum\Base\Enums\Locale;
use Wave8\Factotum\Cms\Policies\TermPolicy;
use Wave8\Factotum\Cms\Traits\HasTranslations;

#[UsePolicy(TermPolicy::class)]
class Term extends Model
{
    use HasTranslations;
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

    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Term::class, 'parent_id', 'id');
    }

    public function contents(): MorphToMany
    {
        return $this->morphedByMany(Content::class, 'termable')->withTimestamps();
    }
}
