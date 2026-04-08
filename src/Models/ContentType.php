<?php

namespace Wave8\Factotum\Cms\Models;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Wave8\Factotum\Cms\Policies\ContentTypePolicy;

#[UsePolicy(ContentTypePolicy::class)]
class ContentType extends Model
{
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

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class);
    }
}
