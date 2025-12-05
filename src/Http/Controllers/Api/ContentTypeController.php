<?php

namespace Wave8\Factotum\Cms\Http\Controllers\Api;

use Wave8\Factotum\Base\Http\Responses\Api\ApiResponse;
use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\ContentType\CreateContentTypeDto;
use Wave8\Factotum\Cms\Http\Requests\Api\ContentType\CreateContentTypeRequest;
use Wave8\Factotum\Cms\Resources\Api\ContentTypeResource;
use Wave8\Factotum\Cms\Services\Api\ContentTypeService;

final readonly class ContentTypeController
{
    public string $contentTypeResource;

    public function __construct(
        /** @var $contentTypeService ContentTypeService */
        private ContentTypeServiceInterface $contentTypeService,
    ) {
        $this->contentTypeResource = config('data_transfer.'.ContentTypeResource::class);
    }

    public function store(CreateContentTypeRequest $request): ApiResponse
    {
        $contentType = $this->contentTypeService->create(
            data: CreateContentTypeDto::from($request)
        );

        return ApiResponse::make(
            data: $this->contentTypeResource::from($contentType),
            status: ApiResponse::HTTP_CREATED
        );
    }
}
