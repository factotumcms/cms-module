<?php

namespace Wave8\Factotum\Cms\Http\Requests\Api\Taxonomy;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AttachContentTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'is_required' => ['sometimes', 'boolean'],
            'allow_multiple' => ['sometimes', 'boolean'],
        ];
    }
}
