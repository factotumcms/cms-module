<?php

namespace Wave8\Factotum\Cms\Http\Controllers\Api;

use Wave8\Factotum\Base\Http\Responses\Api\ApiResponse;
use Wave8\Factotum\Cms\Contracts\Api\ContentFieldServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\ContentField\CreateContentFieldDto;
use Wave8\Factotum\Cms\Dtos\Api\ContentField\UpdateContentFieldDto;
use Wave8\Factotum\Cms\Http\Requests\Api\ContentField\CreateContentFieldRequest;
use Wave8\Factotum\Cms\Http\Requests\Api\ContentField\UpdateContentFieldRequest;
use Wave8\Factotum\Cms\Models\ContentField;
use Wave8\Factotum\Cms\Models\ContentType;
use Wave8\Factotum\Cms\Resources\Api\ContentFieldResource;
use Wave8\Factotum\Cms\Services\Api\ContentFieldService;

final readonly class ContentFieldController
{
    public string $contentFieldResource;

    public function __construct(
        /** @var $contentTypeService ContentFieldService */
        private ContentFieldServiceInterface $contentFieldService,
    ) {
        $this->contentFieldResource = config('data_transfer.'.ContentFieldResource::class);
    }

    public function read(ContentType $contentType, ContentField $contentField): ApiResponse
    {
        if(!$contentType->contentFields()->where('id', $contentField->id)->exists()) {
            return ApiResponse::notFound();
        }

        return ApiResponse::make(
            data: $this->contentFieldResource::from($contentField)
        );
    }

    public function store(ContentType $contentType, CreateContentFieldRequest $request): ApiResponse
    {
        $createContentFieldDto = config('data_transfer.'.CreateContentFieldDto::class);

        $contentField = $this->contentFieldService->createFieldForContentType(
            contentType: $contentType,
            data: $createContentFieldDto::from($request->validated())
        );

        return ApiResponse::make(
            data: $this->contentFieldResource::from($contentField),
            status: ApiResponse::HTTP_CREATED
        );
    }

    public function update(ContentType $contentType, ContentField $contentField, UpdateContentFieldRequest $request): ApiResponse
    {
        if(!$contentType->contentFields()->where('id', $contentField->id)->exists()) {
            return ApiResponse::notFound();
        }

        $updateContentFieldDto = config('data_transfer.'.UpdateContentFieldDto::class);

        $contentField = $this->contentFieldService->update(
            contentField: $contentField,
            data: $updateContentFieldDto::from($request->validated())
        );

        return ApiResponse::make(
            data: $this->contentFieldResource::from($contentField),
            status: ApiResponse::HTTP_OK
        );
    }
}
