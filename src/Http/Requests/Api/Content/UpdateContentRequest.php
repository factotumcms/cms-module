<?php

namespace Wave8\Factotum\Cms\Http\Requests\Api\Content;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Wave8\Factotum\Cms\Rules\Content\ContentEditorTypeRule;
use Wave8\Factotum\Cms\Rules\Content\ContentLangRule;
use Wave8\Factotum\Cms\Rules\Content\ContentStatusRule;

class UpdateContentRequest extends FormRequest
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
            'status' => ['sometimes', 'string', new ContentStatusRule],
            'title' => ['sometimes', 'string'],
            'editor_type' => ['sometimes', 'string', new ContentEditorTypeRule],
            'content' => ['sometimes', 'string'],
            'url' => ['sometimes', 'string'],
            'name' => ['sometimes', 'string', Rule::unique('contents', 'abs_url')->ignore($this->route('content')->id)],
            'lang' => ['sometimes', 'string', new ContentLangRule],
            'seo_params' => ['sometimes', 'array'],
            'seo_params.*' => ['sometimes', 'string'],
            'social_params' => ['sometimes', 'array'],
            'social_params.*' => ['sometimes', 'string'],
        ];
    }
}
