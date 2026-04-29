<?php

namespace Wave8\Factotum\Cms\Models;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wave8\Factotum\Cms\Policies\ContentTypePolicy;
use Wave8\Factotum\Cms\Traits\HasUrlAliases;

#[UsePolicy(ContentTypePolicy::class)]
class ContentType extends Model
{
    use HasUrlAliases;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'editable',
        'order_no',
        'icon',
        'sitemap',
        'label',
        'visible',
        'hierarchical',
    ];

    protected $casts = [
        'editable' => 'boolean',
        'sitemap' => 'boolean',
        'visible' => 'boolean',
        'hierarchical' => 'boolean',
    ];

    protected $searchable = [
        'type',
    ];

    public function contentFields(): HasMany
    {
        return $this->hasMany(ContentField::class);
    }

    public function taxonomies(): BelongsToMany
    {
        return $this->belongsToMany(Taxonomy::class, 'content_type_taxonomy')
            ->withPivot(['is_required', 'allow_multiple'])
            ->withTimestamps();
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class);
    }
}
