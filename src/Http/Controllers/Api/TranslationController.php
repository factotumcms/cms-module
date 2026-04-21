<?php

namespace Wave8\Factotum\Cms\Http\Controllers\Api;

use Wave8\Factotum\Base\Http\Responses\Api\ApiResponse;
use Wave8\Factotum\Cms\Contracts\Api\TranslationServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\Translation\LinkTranslationDto;
use Wave8\Factotum\Cms\Http\Requests\Api\Translation\LinkTranslationRequest;
use Wave8\Factotum\Cms\Models\Translation;
use Wave8\Factotum\Cms\Resources\Api\TranslationResource;
use Wave8\Factotum\Cms\Services\Api\TranslationService;

final readonly class TranslationController
{
    public string $translationResource;

    public function __construct(
        /** @var $translationService TranslationService */
        private TranslationServiceInterface $translationService,
    ) {
        $this->translationResource = config('data_transfer.'.TranslationResource::class);
    }

    public function link(LinkTranslationRequest $request): ApiResponse
    {
        $linkTranslationDto = config('data_transfer.'.LinkTranslationDto::class);
        $dto = $linkTranslationDto::from($request);

        $source = $this->translationService->resolveModel($dto->sourceType, $dto->sourceId);
        $target = $this->translationService->resolveModel($dto->targetType, $dto->targetId);

        $group = $this->translationService->link(
            source: $source,
            target: $target,
            sourceLocale: $dto->sourceLocale,
            targetLocale: $dto->targetLocale,
        );

        $translations = Translation::forGroup($group)->get();

        return ApiResponse::make(
            data: $this->translationResource::collect($translations),
            status: ApiResponse::HTTP_CREATED
        );
    }

    public function unlink(Translation $translation): ApiResponse
    {
        $this->translationService->unlink($translation->translatable);

        return ApiResponse::noContent();
    }

    public function index(string $type, int $id): ApiResponse
    {
        $model = $this->translationService->resolveModel($type, $id);
        $translations = $this->translationService->getTranslations($model);

        return ApiResponse::make(
            data: $translations
        );
    }

    public function locales(string $type, int $id): ApiResponse
    {
        $model = $this->translationService->resolveModel($type, $id);

        return ApiResponse::make(
            data: [
                'available' => $this->translationService->getAvailableLocales($model),
                'missing' => $this->translationService->getMissingLocales($model),
            ]
        );
    }
}
