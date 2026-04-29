<?php

namespace Wave8\Factotum\Cms\Models;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;
use Wave8\Factotum\Base\Enums\Locale;
use Wave8\Factotum\Cms\Casts\ContentContentCast;
use Wave8\Factotum\Cms\Enums\ContentEditorType;
use Wave8\Factotum\Cms\Enums\ContentStatus;
use Wave8\Factotum\Cms\Policies\ContentPolicy;
use Wave8\Factotum\Cms\Resources\Models\Content\ContentSeoParamsResource;
use Wave8\Factotum\Cms\Resources\Models\Content\ContentSocialParamsResource;
use Wave8\Factotum\Cms\Traits\HasTranslations;
use Wave8\Factotum\Cms\Traits\HasUrlAliases;

#[UsePolicy(ContentPolicy::class)]
class Content extends Model
{
    use HasTranslations;
    use HasUrlAliases;
    use NodeTrait;
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
        'is_visible',
        'order_no',
        'seo_params',
        'social_params',
    ];

    protected $casts = [
        'show_in_menu' => 'boolean',
        'is_home' => 'boolean',
        'is_visible' => 'boolean',
        'builder' => 'array',
        'content' => ContentContentCast::class,
        'editor_type' => ContentEditorType::class,
        'seo_params' => ContentSeoParamsResource::class,
        'social_params' => ContentSocialParamsResource::class,
        'lang' => Locale::class,
        'status' => ContentStatus::class,
    ];

    protected $searchable = [
        'title',
        'content',
    ];

    /**
     * Scope the nested set tree per content type.
     */
    protected function getScopeAttributes(): array
    {
        return ['content_type_id'];
    }

    public function contentType(): BelongsTo
    {
        return $this->belongsTo(ContentType::class, 'content_type_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    public function terms(): MorphToMany
    {
        return $this->morphToMany(Term::class, 'termable')->withTimestamps();
    }

    /**
     * Build the hierarchical URL path for this content.
     * Example: "chi-siamo/il-team" for a nested page.
     */
    public function buildHierarchicalPath(): string
    {
        $ancestors = $this->ancestors()->defaultOrder()->get();

        $segments = $ancestors->pluck('url')->push($this->url);

        return $segments->implode('/');
    }
}
