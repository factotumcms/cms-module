<?php

namespace Wave8\Factotum\Cms\Models;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Wave8\Factotum\Base\Enums\Locale;
use Wave8\Factotum\Cms\Policies\UrlAliasPolicy;

#[UsePolicy(UrlAliasPolicy::class)]
class UrlAlias extends Model
{
    protected $fillable = [
        'uri',
        'routable_type',
        'routable_id',
        'locale',
        'is_canonical',
        'redirect_to',
    ];

    protected $casts = [
        'is_canonical' => 'boolean',
        'locale' => Locale::class,
    ];

    public function routable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeCanonical(Builder $query): Builder
    {
        return $query->where('is_canonical', true);
    }

    public function scopeForLocale(Builder $query, Locale|string $locale): Builder
    {
        $value = $locale instanceof Locale ? $locale->value : $locale;

        return $query->where('locale', $value);
    }

    public function scopeForUri(Builder $query, string $uri): Builder
    {
        return $query->where('uri', $uri);
    }

    public function scopeForRoutable(Builder $query, Model $routable): Builder
    {
        return $query->where('routable_type', get_class($routable))
            ->where('routable_id', $routable->id);
    }
}
