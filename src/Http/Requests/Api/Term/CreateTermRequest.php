<?php

namespace Wave8\Factotum\Cms\Http\Requests\Api\Term;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Wave8\Factotum\Cms\Rules\Content\ContentLangRule;

class CreateTermRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'lang' => ['required', 'string', new ContentLangRule],
            'parent_id' => ['sometimes', 'nullable', 'integer', 'exists:terms,id'],
            'sort_order' => ['sometimes', 'integer'],
        ];
    }
}
