<?php

namespace Wave8\Factotum\Cms\Http\Requests\Api\UrlAlias;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUrlAliasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'uri' => ['sometimes', 'string', 'max:2048'],
            'redirect_to' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'is_canonical' => ['sometimes', 'boolean'],
        ];
    }
}

