<?php

namespace Wave8\Factotum\Cms\Http\Requests\Api\UrlAlias;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Wave8\Factotum\Base\Enums\Locale;

class CreateUrlAliasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'uri' => ['required', 'string', 'max:2048'],
            'routable_type' => ['required', 'string', 'in:content,term,content_type'],
            'routable_id' => ['required', 'integer'],
            'locale' => ['required', Rule::enum(Locale::class)],
            'is_canonical' => ['sometimes', 'boolean'],
        ];
    }
}
