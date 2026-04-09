<?php

namespace Wave8\Factotum\Cms\Http\Controllers\Api;

use Wave8\Factotum\Base\Http\Responses\Api\ApiResponse;
use Wave8\Factotum\Cms\Contracts\Api\ContentServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\Content\CreateContentDto;
use Wave8\Factotum\Cms\Http\Requests\Api\Content\CreateContentRequest;
use Wave8\Factotum\Cms\Resources\Api\ContentResource;
use Wave8\Factotum\Cms\Services\Api\ContentService;

final readonly class ContentController
{
    public string $contentResource;

    public function __construct(
        /** @var $contentService ContentService */
        private ContentServiceInterface $contentService,
    ) {
        $this->contentResource = config('data_transfer.'.ContentResource::class);
    }

    public function store(CreateContentRequest $request): ApiResponse
    {
        $createContentDto = config('data_transfer.'.CreateContentDto::class);

        $content = $this->contentService->create(
            data: $createContentDto::from($request)->additional([
                'user_id' => auth()->id(),
            ])
        );

        return ApiResponse::make(
            data: $this->contentResource::from($content),
            status: ApiResponse::HTTP_CREATED
        );
    }
}
