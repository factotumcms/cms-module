<?php

namespace Wave8\Factotum\Cms\Models;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Wave8\Factotum\Base\Enums\Locale;
use Wave8\Factotum\Cms\Policies\TranslationPolicy;

#[UsePolicy(TranslationPolicy::class)]
class Translation extends Model
{
    protected $fillable = [
        'translation_group',
        'translatable_id',
        'translatable_type',
        'locale',
    ];

    protected $casts = [
        'locale' => Locale::class,
    ];

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForGroup($query, string $group)
    {
        return $query->where('translation_group', $group);
    }
}
