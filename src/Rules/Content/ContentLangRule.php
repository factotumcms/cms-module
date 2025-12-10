<?php

namespace Wave8\Factotum\Cms\Rules\Content;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Wave8\Factotum\Base\Contracts\Api\SettingServiceInterface;
use Wave8\Factotum\Base\Enums\Setting\Setting;
use Wave8\Factotum\Base\Enums\Setting\SettingGroup;
use Wave8\Factotum\Base\Services\Api\SettingService;

class ContentLangRule implements ValidationRule
{
    private array $availableLocales;

    /** @var SettingService */
    private SettingServiceInterface $settingService;

    public function __construct()
    {
        $this->settingService = app(SettingServiceInterface::class);

        $this->availableLocales = $this->settingService->getValue(Setting::AVAILABLE_LOCALES, SettingGroup::LOCALE);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! in_array($value, $this->availableLocales)) {
            $fail(__('validation.content_editor_type_rule', [
                'attribute' => $attribute,
                'values' => implode(', ', $this->availableLocales),
            ]));
        }
    }
}
