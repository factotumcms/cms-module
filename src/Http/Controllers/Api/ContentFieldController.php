<?php

namespace Wave8\Factotum\Cms\Http\Controllers\Api;

use Wave8\Factotum\Base\Http\Responses\Api\ApiResponse;
use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\ContentField\CreateContentFieldDto;
use Wave8\Factotum\Cms\Http\Requests\Api\ContentField\CreateContentFieldRequest;
use Wave8\Factotum\Cms\Resources\Api\ContentFieldResource;
use Wave8\Factotum\Cms\Services\Api\ContentTypeService;

final readonly class ContentFieldController
{
    public string $contentFieldResource;

    public function __construct(
        /** @var $contentTypeService ContentTypeService */
        private ContentTypeServiceInterface $contentTypeService,
    ) {
        $this->contentFieldResource = config('data_transfer.'.ContentFieldResource::class);
    }

    public function store(CreateContentFieldRequest $request): ApiResponse
    {
        $contentType = $this->contentTypeService->getByType(
            type: $request->validated('content_type')
        );

        $contentField = $this->contentTypeService->createFieldForContentType(
            contentType: $contentType,
            data: CreateContentFieldDto::from($request->validated())
        );

        return ApiResponse::make(
            data: $this->contentFieldResource::from($contentField),
            status: ApiResponse::HTTP_CREATED
        );
    }
}
