<?php

namespace Wave8\Factotum\Cms\Http\Requests\Api\Content;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Wave8\Factotum\Base\Contracts\Api\SettingServiceInterface;
use Wave8\Factotum\Base\Enums\Setting\Setting;
use Wave8\Factotum\Base\Enums\Setting\SettingGroup;
use Wave8\Factotum\Base\Services\Api\SettingService;
use Wave8\Factotum\Cms\Enums\ContentEditorType;
use Wave8\Factotum\Cms\Enums\ContentStatus;

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
            'status' => ['required', 'string'],
            'title' => ['required', 'string'],
            'editor_type' => ['required', 'string'],
            'content' => ['required', 'string'],
            'url' => ['required', 'string', 'unique:contents,url'],
            'abs_url' => ['required', 'string', 'unique:contents,abs_url'],
            'lang' => ['required', 'string'],
        ];

        $rules['status'][] = 'in:'.implode(',', ContentStatus::getValues()->toArray());
        $rules['editor_type'][] = 'in:'.implode(',', ContentEditorType::getValues()->toArray());

        /* @var SettingService $settingService */
        $settingService = app(SettingServiceInterface::class);
        $availableLanguages = $settingService->getValue(Setting::AVAILABLE_LOCALES, SettingGroup::LOCALE);
        $rules['lang'][] = 'in:'.implode(',', $availableLanguages);

        return $rules;
    }
}
