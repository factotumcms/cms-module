<?php

namespace Wave8\Factotum\Cms\Http\Requests\Api\Term;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SyncTermsRequest extends FormRequest
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
            'term_ids' => ['required', 'array'],
            'term_ids.*' => ['integer', 'exists:terms,id'],
        ];
    }
}
