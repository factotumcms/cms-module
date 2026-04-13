<?php

namespace Wave8\Factotum\Cms\Http\Requests\Api\ContentField;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Wave8\Factotum\Cms\Enums\ContentFieldType;

class UpdateContentFieldRequest extends FormRequest
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
            // Safe fields
            'label' => ['sometimes', 'string'],
            'type' => ['sometimes', 'string', 'in:'.implode(',', ContentFieldType::getValues()->toArray())],
            'hint' => ['sometimes', 'nullable', 'string'],
            'visibility_rules' => ['sometimes', 'nullable', 'string'],
            'mandatory_rules' => ['sometimes', 'nullable', 'string'],
            'configs' => ['sometimes', 'nullable', 'array'],
            'order_no' => ['sometimes', 'int'],
            'mandatory' => ['sometimes', 'boolean'],
            'readonly' => ['sometimes', 'boolean'],

            // Critical fields
            'name' => ['sometimes', 'string', Rule::unique('content_fields', 'name')->ignore($this->route('contentField')->id)],
        ];
    }
}
