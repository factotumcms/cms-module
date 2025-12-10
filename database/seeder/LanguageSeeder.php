<?php

namespace Wave8\Factotum\Cms\Database\Seeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Wave8\Factotum\Base\Contracts\Api\LanguageServiceInterface;
use Wave8\Factotum\Base\Dtos\Api\Language\RegisterLineDto;
use Wave8\Factotum\Base\Enums\Locale;

class LanguageSeeder extends Seeder
{
    public function __construct(
        private readonly LanguageServiceInterface $languageService
    ) {}

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->languageService->create(
            data: new RegisterLineDto(
                locale: Locale::IT,
                group: 'validation',
                key: 'content_editor_type_rule',
                line: 'Il campo :attribute non è valido, deve essere uno tra: :values.',
            )
        );

        $this->languageService->create(
            data: new RegisterLineDto(
                locale: Locale::EN,
                group: 'validation',
                key: 'content_editor_type_rule',
                line: 'The :attribute field is invalid, must be one of: :values.',
            )
        );

        $this->languageService->create(
            data: new RegisterLineDto(
                locale: Locale::IT,
                group: 'validation',
                key: 'content_lang_rule',
                line: 'Il campo :attribute non è valido, deve essere uno tra: :values.',
            )
        );

        $this->languageService->create(
            data: new RegisterLineDto(
                locale: Locale::EN,
                group: 'validation',
                key: 'content_lang_rule',
                line: 'The :attribute field is invalid, must be one of: :values.',
            )
        );

        $this->languageService->create(
            data: new RegisterLineDto(
                locale: Locale::IT,
                group: 'validation',
                key: 'content_status_rule',
                line: 'Il campo :attribute non è valido, deve essere uno tra: :values.',
            )
        );

        $this->languageService->create(
            data: new RegisterLineDto(
                locale: Locale::EN,
                group: 'validation',
                key: 'content_status_rule',
                line: 'The :attribute field is invalid, must be one of: :values.',
            )
        );

        $this->languageService->create(
            data: new RegisterLineDto(
                locale: Locale::IT,
                group: 'validation',
                key: 'content_type_table_unique_rule',
                line: 'Il campo :attribute non è valido, esiste già una tabella con questo nome',
            )
        );

        $this->languageService->create(
            data: new RegisterLineDto(
                locale: Locale::EN,
                group: 'validation',
                key: 'content_type_table_unique_rule',
                line: 'The :attribute is invalid, a database table with this name already exists',
            )
        );
    }
}
