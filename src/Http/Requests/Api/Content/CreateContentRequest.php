<?php

namespace Wave8\Factotum\Cms\Http\Requests\Api\Content;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Wave8\Factotum\Cms\Rules\Content\ContentEditorTypeRule;
use Wave8\Factotum\Cms\Rules\Content\ContentLangRule;
use Wave8\Factotum\Cms\Rules\Content\ContentStatusRule;

class CreateContentRequest extends FormRequest
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
        $rules = [
            'content_type_id' => ['required', 'int', 'exists:content_types,id'],
            'parent_id' => ['sometimes', 'int', 'exists:contents,id'],
            'status' => ['required', 'string', new ContentStatusRule],
            'title' => ['required', 'string'],
            'editor_type' => ['required', 'string', new ContentEditorTypeRule],
            'content' => ['required', 'string'],
            'url' => ['required', 'string', 'unique:contents,url'],
            'abs_url' => ['required', 'string', 'unique:contents,abs_url'],
            'lang' => ['required', 'string', new ContentLangRule],
        ];

        return $rules;
    }
}
