<?php

namespace Wave8\Factotum\Cms\Services\Api;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Wave8\Factotum\Base\Enums\Locale;
use Wave8\Factotum\Cms\Contracts\Api\UrlAliasServiceInterface;
use Wave8\Factotum\Cms\Models\UrlAlias;

readonly class UrlAliasService implements UrlAliasServiceInterface
{
    public function __construct(public UrlAlias $model) {}

    public function resolve(string $uri, Locale|string $locale): ?UrlAlias
    {
        $normalizedUri = '/'.trim($uri, '/');
        $localeValue = $locale instanceof Locale ? $locale->value : $locale;

        return $this->model::query()
            ->forUri($normalizedUri)
            ->forLocale($localeValue)
            ->with('routable')
            ->first();
    }

    public function createForRoutable(Model $routable, string $uri, Locale|string $locale, bool $isCanonical = true): UrlAlias
    {
        $normalizedUri = '/'.trim($uri, '/');
        $localeValue = $locale instanceof Locale ? $locale->value : $locale;

        // If setting as canonical, demote existing canonical for this routable + locale
        if ($isCanonical) {
            $this->demoteExistingCanonical($routable, $localeValue);
        }

        return $this->model::create([
            'uri' => $normalizedUri,
            'routable_type' => get_class($routable),
            'routable_id' => $routable->id,
            'locale' => $localeValue,
            'is_canonical' => $isCanonical,
        ]);
    }

    public function updateCanonical(Model $routable, string $newUri, Locale|string $locale): UrlAlias
    {
        $normalizedUri = '/'.trim($newUri, '/');
        $localeValue = $locale instanceof Locale ? $locale->value : $locale;

        $existingCanonical = $this->model::query()
            ->forRoutable($routable)
            ->forLocale($localeValue)
            ->canonical()
            ->first();

        // If the canonical URI hasn't changed, return as-is
        if ($existingCanonical && $existingCanonical->uri === $normalizedUri) {
            return $existingCanonical;
        }

        // Demote the old canonical and set redirect
        if ($existingCanonical) {
            $existingCanonical->update([
                'is_canonical' => false,
                'redirect_to' => $normalizedUri,
            ]);
        }

        // Check if the new URI already exists for this routable (e.g. old alias being re-promoted)
        $existingAlias = $this->model::query()
            ->forRoutable($routable)
            ->forUri($normalizedUri)
            ->forLocale($localeValue)
            ->first();

        if ($existingAlias) {
            $existingAlias->update([
                'is_canonical' => true,
                'redirect_to' => null,
            ]);

            return $existingAlias;
        }

        return $this->model::create([
            'uri' => $normalizedUri,
            'routable_type' => get_class($routable),
            'routable_id' => $routable->id,
            'locale' => $localeValue,
            'is_canonical' => true,
        ]);
    }

    public function deleteForRoutable(Model $routable): void
    {
        $this->model::query()
            ->forRoutable($routable)
            ->delete();
    }

    public function getForRoutable(Model $routable): Collection
    {
        return $this->model::query()
            ->forRoutable($routable)
            ->orderByDesc('is_canonical')
            ->get();
    }

    public function setRedirect(UrlAlias $alias, string $targetUri): UrlAlias
    {
        $normalizedUri = '/'.trim($targetUri, '/');

        $alias->update([
            'redirect_to' => $normalizedUri,
            'is_canonical' => false,
        ]);

        return $alias;
    }

    public function delete(UrlAlias $alias): bool
    {
        return $alias->delete();
    }

    /**
     * Demote existing canonical aliases for a routable + locale combination.
     */
    private function demoteExistingCanonical(Model $routable, string $locale): void
    {
        $this->model::query()
            ->forRoutable($routable)
            ->forLocale($locale)
            ->canonical()
            ->update(['is_canonical' => false]);
    }
}

