<?php

namespace Wave8\Factotum\Cms\Http\Requests\Api\Translation;

use Illuminate\Foundation\Http\FormRequest;
use Wave8\Factotum\Base\Enums\Locale;

class LinkTranslationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $locales = implode(',', array_column(Locale::cases(), 'value'));
        $types = 'content,term';

        return [
            'source_id' => ['required', 'integer'],
            'source_type' => ['required', 'string', "in:{$types}"],
            'target_id' => ['required', 'integer'],
            'target_type' => ['required', 'string', "in:{$types}"],
            'source_locale' => ['required', 'string', "in:{$locales}"],
            'target_locale' => ['required', 'string', "in:{$locales}", 'different:source_locale'],
        ];
    }

    /**
     * Additional validation after standard rules pass.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('source_type') !== $this->input('target_type')) {
                $validator->errors()->add('target_type', 'Source and target must be of the same type.');
            }
        });
    }
}
