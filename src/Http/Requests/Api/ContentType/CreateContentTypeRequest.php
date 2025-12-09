<?php

namespace Wave8\Factotum\Cms\Http\Requests\Api\ContentType;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Wave8\Factotum\Cms\Rules\ContentType\ContentTypeTableUniqueRule;

class CreateContentTypeRequest extends FormRequest
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
            'label' => ['required', 'string'],
            'type' => ['required', 'string', 'unique:content_types,type', new ContentTypeTableUniqueRule],
            'editable' => ['required', 'boolean'],
            'order_no' => ['required', 'int'],
            'sitemap' => ['required', 'boolean'],
            'visible' => ['required', 'boolean'],
            'hierarchical' => ['required', 'boolean'],
            'icon' => ['sometimes', 'string'],
        ];
    }
}
