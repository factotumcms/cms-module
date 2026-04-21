<?php

namespace Wave8\Factotum\Cms\Contracts\Api;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Wave8\Factotum\Base\Enums\Locale;

interface TranslationServiceInterface
{
    public function link(Model $source, Model $target, Locale $sourceLocale, Locale $targetLocale): string;

    public function unlink(Model $model): void;

    public function getTranslations(Model $model): Collection;

    public function getTranslation(Model $model, Locale $locale): ?Model;

    public function getAvailableLocales(Model $model): array;

    public function getMissingLocales(Model $model): array;

    public function resolveModel(string $type, int $id): Model;
}
