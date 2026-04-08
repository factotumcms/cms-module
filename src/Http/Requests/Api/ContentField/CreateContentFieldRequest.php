<?php

namespace Wave8\Factotum\Cms\Http\Requests\Api\ContentField;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Wave8\Factotum\Cms\Enums\ContentFieldType;

class CreateContentFieldRequest extends FormRequest
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
            'content_type' => 'required|string|exists:content_types,type',
            'name' => ['required', 'string', 'unique:content_fields,name'],
            'label' => ['required', 'string'],
            'type' => ['required', 'string', 'in:'.implode(',', ContentFieldType::getValues()->toArray())],

            'hint' => ['nullable', 'string'],
            'visibility_rules' => ['nullable', 'string'],
            'mandatory_rules' => ['nullable', 'string'],
            'configs' => ['nullable', 'array'],

            'order_no' => ['sometimes', 'int'],
            'mandatory' => ['sometimes', 'boolean'],
            'readonly' => ['sometimes', 'boolean'],
        ];
    }
}
