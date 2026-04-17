<?php

namespace Wave8\Factotum\Cms\Models;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wave8\Factotum\Base\Enums\Locale;
use Wave8\Factotum\Cms\Casts\ContentContentCast;
use Wave8\Factotum\Cms\Enums\ContentEditorType;
use Wave8\Factotum\Cms\Policies\ContentPolicy;
use Wave8\Factotum\Cms\Resources\Models\Content\ContentSeoParamsResource;
use Wave8\Factotum\Cms\Resources\Models\Content\ContentSocialParamsResource;

#[UsePolicy(ContentPolicy::class)]
class Content extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'content_type_id',
        'user_id',
        'parent_id',
        'status',
        'title',
        'content',
        'builder',
        'editor_type',
        'url',
        'abs_url',
        'lang',
        'show_in_menu',
        'is_home',
        'order_no',
        'seo_params',
        'social_params',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'show_in_menu' => 'boolean',
        'is_home' => 'boolean',
        'builder' => 'array',
        'content' => ContentContentCast::class,
        'editor_type' => ContentEditorType::class,
        'seo_params' => ContentSeoParamsResource::class,
        'social_params' => ContentSocialParamsResource::class,
        'locale' => Locale::class,
    ];

    protected $searchable = [
        'title',
        'content',
    ];

    public function contentType(): BelongsTo
    {
        return $this->belongsTo(ContentType::class, 'content_type_id', 'id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Content::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Content::class, 'parent_id', 'id');
    }
}
