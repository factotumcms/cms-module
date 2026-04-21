<?php

namespace Wave8\Factotum\Cms\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Wave8\Factotum\Base\Enums\Locale;
use Wave8\Factotum\Cms\Models\Translation;

trait HasTranslations
{
    public function translation(): MorphOne
    {
        return $this->morphOne(Translation::class, 'translatable');
    }

    public function translationGroup(): ?string
    {
        return $this->translation?->translation_group;
    }

    public function translatedSiblings(): Collection
    {
        $group = $this->translationGroup();

        if (! $group) {
            return new Collection([$this]);
        }

        $translations = Translation::forGroup($group)
            ->with('translatable')
            ->get();

        return $translations->map(fn (Translation $t) => $t->translatable)->filter();
    }

    public function getTranslation(Locale $locale): ?static
    {
        $group = $this->translationGroup();

        if (! $group) {
            return null;
        }

        $translation = Translation::forGroup($group)
            ->where('locale', $locale->value)
            ->with('translatable')
            ->first();

        return $translation?->translatable;
    }

    public function isTranslatedIn(Locale $locale): bool
    {
        return $this->getTranslation($locale) !== null;
    }

    public function availableLocales(): array
    {
        $group = $this->translationGroup();

        if (! $group) {
            return $this->lang ? [$this->lang] : [];
        }

        return Translation::forGroup($group)
            ->pluck('locale')
            ->toArray();
    }

    public function missingLocales(): array
    {
        $available = $this->availableLocales();

        return collect(Locale::cases())
            ->map(fn (Locale $locale) => $locale->value)
            ->diff($available)
            ->values()
            ->toArray();
    }
}
