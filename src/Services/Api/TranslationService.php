<?php

namespace Wave8\Factotum\Cms\Services\Api;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Wave8\Factotum\Base\Enums\Locale;
use Wave8\Factotum\Cms\Contracts\Api\TranslationServiceInterface;
use Wave8\Factotum\Cms\Models\Content;
use Wave8\Factotum\Cms\Models\Term;
use Wave8\Factotum\Cms\Models\Translation;

readonly class TranslationService implements TranslationServiceInterface
{
    private const array TRANSLATABLE_MAP = [
        'content' => Content::class,
        'term' => Term::class,
    ];

    public function link(Model $source, Model $target, Locale $sourceLocale, Locale $targetLocale): string
    {
        if ($sourceLocale === $targetLocale) {
            throw new \InvalidArgumentException('Source and target locales must be different.');
        }

        if (get_class($source) !== get_class($target)) {
            throw new \InvalidArgumentException('Source and target must be of the same type.');
        }

        if ($source->id === $target->id) {
            throw new \InvalidArgumentException('Source and target must be different records.');
        }

        // Determine translation group: use existing or create new
        $sourceTranslation = Translation::where('translatable_id', $source->id)
            ->where('translatable_type', get_class($source))
            ->first();

        $targetTranslation = Translation::where('translatable_id', $target->id)
            ->where('translatable_type', get_class($target))
            ->first();

        if ($sourceTranslation && $targetTranslation) {
            if ($sourceTranslation->translation_group === $targetTranslation->translation_group) {
                return $sourceTranslation->translation_group;
            }

            throw new \InvalidArgumentException('Both records already belong to different translation groups.');
        }

        $group = $sourceTranslation?->translation_group
            ?? $targetTranslation?->translation_group
            ?? (string) Str::uuid();

        // Upsert source
        if (! $sourceTranslation) {
            Translation::create([
                'translation_group' => $group,
                'translatable_id' => $source->id,
                'translatable_type' => get_class($source),
                'locale' => $sourceLocale->value,
            ]);
        }

        // Upsert target
        if (! $targetTranslation) {
            Translation::create([
                'translation_group' => $group,
                'translatable_id' => $target->id,
                'translatable_type' => get_class($target),
                'locale' => $targetLocale->value,
            ]);
        }

        return $group;
    }

    public function unlink(Model $model): void
    {
        $translation = Translation::where('translatable_id', $model->id)
            ->where('translatable_type', get_class($model))
            ->first();

        if (! $translation) {
            return;
        }

        $group = $translation->translation_group;
        $translation->delete();

        // Cleanup: if only one record remains in the group, remove it too
        $remaining = Translation::forGroup($group)->count();
        if ($remaining === 1) {
            Translation::forGroup($group)->delete();
        }
    }

    public function getTranslations(Model $model): Collection
    {
        $translation = Translation::where('translatable_id', $model->id)
            ->where('translatable_type', get_class($model))
            ->first();

        if (! $translation) {
            return new Collection;
        }

        return Translation::forGroup($translation->translation_group)
            ->with('translatable')
            ->get()
            ->map(fn (Translation $t) => $t->translatable)
            ->filter()
            ->values();
    }

    public function getTranslation(Model $model, Locale $locale): ?Model
    {
        $translation = Translation::where('translatable_id', $model->id)
            ->where('translatable_type', get_class($model))
            ->first();

        if (! $translation) {
            return null;
        }

        $target = Translation::forGroup($translation->translation_group)
            ->where('locale', $locale->value)
            ->with('translatable')
            ->first();

        return $target?->translatable;
    }

    public function getAvailableLocales(Model $model): array
    {
        $translation = Translation::where('translatable_id', $model->id)
            ->where('translatable_type', get_class($model))
            ->first();

        if (! $translation) {
            return [];
        }

        return Translation::forGroup($translation->translation_group)
            ->pluck('locale')
            ->map(fn (Locale $locale) => $locale->value)
            ->toArray();
    }

    public function getMissingLocales(Model $model): array
    {
        $available = $this->getAvailableLocales($model);

        return collect(Locale::cases())
            ->map(fn (Locale $locale) => $locale->value)
            ->diff($available)
            ->values()
            ->toArray();
    }

    public function resolveModel(string $type, int $id): Model
    {
        $fqcn = self::TRANSLATABLE_MAP[$type] ?? null;

        if (! $fqcn) {
            throw new \InvalidArgumentException("Unknown translatable type: {$type}");
        }

        return $fqcn::findOrFail($id);
    }
}
