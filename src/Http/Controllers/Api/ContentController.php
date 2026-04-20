<?php

namespace Wave8\Factotum\Cms\Http\Controllers\Api;

use Illuminate\Support\Facades\Gate;
use Wave8\Factotum\Base\Http\Responses\Api\ApiResponse;
use Wave8\Factotum\Cms\Contracts\Api\ContentServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\Content\CreateContentDto;
use Wave8\Factotum\Cms\Dtos\Api\Content\UpdateContentDto;
use Wave8\Factotum\Cms\Http\Requests\Api\Content\CreateContentRequest;
use Wave8\Factotum\Cms\Http\Requests\Api\Content\UpdateContentRequest;
use Wave8\Factotum\Cms\Models\Content;
use Wave8\Factotum\Cms\Models\ContentType;
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

    public function read(ContentType $contentType, Content $content): ApiResponse
    {
        if (! $contentType->contents()->where('id', $content->id)->exists()) {
            return ApiResponse::notFound();
        }

        return ApiResponse::make(
            data: $this->contentResource::from($content)->additional([
                'fields' => $this->contentService->getDynamicFields($content),
            ])
        );
    }

    public function store(CreateContentRequest $request, ContentType $contentType): ApiResponse
    {
        $createContentDto = config('data_transfer.'.CreateContentDto::class);

        $content = $this->contentService->createContentForContentType(
            contentType: $contentType,
            data: $createContentDto::from($request)->additional([
                'user_id' => auth()->id(),
            ])
        );

        return ApiResponse::make(
            data: $this->contentResource::from($content),
            status: ApiResponse::HTTP_CREATED
        );
    }

    public function update(ContentType $contentType, Content $content, UpdateContentRequest $request): ApiResponse
    {
        if (! $contentType->contents()->where('id', $content->id)->exists()) {
            return ApiResponse::notFound();
        }

        $updateContentDto = config('data_transfer.'.UpdateContentDto::class);

        $this->contentService->update($content, $updateContentDto::from($request)->additional([
            'user_id' => auth()->id(),
        ]));

        return ApiResponse::make(
            data: $this->contentResource::from($content),
            status: ApiResponse::HTTP_OK
        );
    }

    public function destroy(ContentType $contentType, Content $content): ApiResponse
    {
        Gate::allowIf(function () use ($contentType, $content) {
            return $contentType->contents()->where('id', $content->id)->exists();
        });

        $this->contentService->delete($content);

        return ApiResponse::noContent();
    }
}
