<?php

namespace Wave8\Factotum\Cms\Http\Controllers\Api;

use Wave8\Factotum\Base\Http\Responses\Api\ApiResponse;
use Wave8\Factotum\Cms\Contracts\Api\TaxonomyServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\Taxonomy\CreateTaxonomyDto;
use Wave8\Factotum\Cms\Dtos\Api\Taxonomy\UpdateTaxonomyDto;
use Wave8\Factotum\Cms\Http\Requests\Api\Taxonomy\AttachContentTypeRequest;
use Wave8\Factotum\Cms\Http\Requests\Api\Taxonomy\CreateTaxonomyRequest;
use Wave8\Factotum\Cms\Http\Requests\Api\Taxonomy\UpdateTaxonomyRequest;
use Wave8\Factotum\Cms\Models\ContentType;
use Wave8\Factotum\Cms\Models\Taxonomy;
use Wave8\Factotum\Cms\Resources\Api\TaxonomyResource;
use Wave8\Factotum\Cms\Services\Api\TaxonomyService;

final readonly class TaxonomyController
{
    public string $taxonomyResource;

    public function __construct(
        /** @var $taxonomyService TaxonomyService */
        private TaxonomyServiceInterface $taxonomyService,
    ) {
        $this->taxonomyResource = config('data_transfer.'.TaxonomyResource::class);
    }

    public function read(Taxonomy $taxonomy): ApiResponse
    {
        return ApiResponse::make(
            data: $this->taxonomyResource::from($taxonomy)
        );
    }

    public function store(CreateTaxonomyRequest $request): ApiResponse
    {
        $createTaxonomyDto = config('data_transfer.'.CreateTaxonomyDto::class);

        $taxonomy = $this->taxonomyService->create(
            data: $createTaxonomyDto::from($request)
        );

        return ApiResponse::make(
            data: $this->taxonomyResource::from($taxonomy),
            status: ApiResponse::HTTP_CREATED
        );
    }

    public function update(Taxonomy $taxonomy, UpdateTaxonomyRequest $request): ApiResponse
    {
        $updateTaxonomyDto = config('data_transfer.'.UpdateTaxonomyDto::class);

        $taxonomy = $this->taxonomyService->update(
            taxonomy: $taxonomy,
            data: $updateTaxonomyDto::from($request)
        );

        return ApiResponse::make(
            data: $this->taxonomyResource::from($taxonomy)
        );
    }

    public function destroy(Taxonomy $taxonomy): ApiResponse
    {
        $this->taxonomyService->delete($taxonomy);

        return ApiResponse::noContent();
    }

    public function attachContentType(Taxonomy $taxonomy, ContentType $contentType, AttachContentTypeRequest $request): ApiResponse
    {
        $this->taxonomyService->attachToContentType(
            taxonomy: $taxonomy,
            contentType: $contentType,
            isRequired: $request->boolean('is_required', false),
            allowMultiple: $request->boolean('allow_multiple', true),
        );

        return ApiResponse::make(
            data: $this->taxonomyResource::from($taxonomy->load('contentTypes'))
        );
    }

    public function detachContentType(Taxonomy $taxonomy, ContentType $contentType): ApiResponse
    {
        $this->taxonomyService->detachFromContentType(
            taxonomy: $taxonomy,
            contentType: $contentType,
        );

        return ApiResponse::noContent();
    }
}
