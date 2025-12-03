<?php

namespace Wave8\Factotum\Cms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'content_type_id',
        'parent_id',
        'name',
        'label',
        'abs_url',
        'description',
        'image',
        'lang',
        'order_no',
        'sitemap',
        'seo_title',
        'seo_description',
        'seo_canonical_url',
        'seo_robots_indexing',
        'seo_robots_following',
        'seo_focus_key',
        'fb_title',
        'fb_description',
        'fb_image',
    ];

    protected $casts = [
        'sitemap' => 'boolean',
    ];

    protected $searchable = [
        'label',
        'name',
        'description',
    ];

    public function content_type(): BelongsTo
    {
        return $this->belongsTo(ContentType::class);
    }

    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class)->withTimestamps();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }
}
