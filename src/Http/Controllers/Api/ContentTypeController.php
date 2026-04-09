<?php

namespace Wave8\Factotum\Cms\Http\Controllers\Api;

use Wave8\Factotum\Base\Http\Responses\Api\ApiResponse;
use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\ContentField\CreateContentFieldDto;
use Wave8\Factotum\Cms\Dtos\Api\ContentType\CreateContentTypeDto;
use Wave8\Factotum\Cms\Dtos\Api\ContentType\UpdateContentTypeDto;
use Wave8\Factotum\Cms\Http\Requests\Api\ContentField\CreateContentFieldRequest;
use Wave8\Factotum\Cms\Http\Requests\Api\ContentType\CreateContentTypeRequest;
use Wave8\Factotum\Cms\Http\Requests\Api\ContentType\UpdateContentTypeRequest;
use Wave8\Factotum\Cms\Models\ContentType;
use Wave8\Factotum\Cms\Resources\Api\ContentFieldResource;
use Wave8\Factotum\Cms\Resources\Api\ContentTypeResource;
use Wave8\Factotum\Cms\Services\Api\ContentTypeService;

final readonly class ContentTypeController
{
    public string $contentTypeResource;

    public string $contentFieldResource;

    public function __construct(
        /** @var $contentTypeService ContentTypeService */
        private ContentTypeServiceInterface $contentTypeService,
    ) {
        $this->contentTypeResource = config('data_transfer.'.ContentTypeResource::class);
        $this->contentFieldResource = config('data_transfer.'.ContentFieldResource::class);
    }

    public function store(CreateContentTypeRequest $request): ApiResponse
    {
        $createContentTypeDto = config('data_transfer.'.CreateContentTypeDto::class);

        $contentType = $this->contentTypeService->create(
            data: $createContentTypeDto::from($request)
        );

        return ApiResponse::make(
            data: $this->contentTypeResource::from($contentType),
            status: ApiResponse::HTTP_CREATED
        );
    }

    public function update(ContentType $contentType, UpdateContentTypeRequest $request): ApiResponse
    {
        $updateContentTypeDto = config('data_transfer.'.UpdateContentTypeDto::class);

        $contentType = $this->contentTypeService->update(
            contentType: $contentType,
            data: $updateContentTypeDto::from($request)
        );

        return ApiResponse::make(
            data: $this->contentTypeResource::from($contentType)
        );
    }

    public function createContentField(ContentType $contentType, CreateContentFieldRequest $request): ApiResponse
    {
        $createContentFieldDto = config('data_transfer.'.CreateContentFieldDto::class);

        $contentField = $this->contentTypeService->createFieldForContentType(
            contentType: $contentType,
            data: $createContentFieldDto::from($request->validated())
        );

        return ApiResponse::make(
            data: $this->contentFieldResource::from($contentField),
            status: ApiResponse::HTTP_CREATED
        );
    }
}
