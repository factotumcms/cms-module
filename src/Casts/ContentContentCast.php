<?php

namespace Wave8\Factotum\Cms\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Wave8\Factotum\Cms\Enums\ContentEditorType;

class ContentContentCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): array|string|null
    {
        if ($model->editor_type == ContentEditorType::BUILDER) {
            return json_decode($value, true);
        }

        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($model->editor_type == ContentEditorType::BUILDER) {
            return json_encode($value);
        }

        return $value;
    }
}
