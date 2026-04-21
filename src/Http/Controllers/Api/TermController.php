<?php

namespace Wave8\Factotum\Cms\Http\Controllers\Api;

use Wave8\Factotum\Base\Http\Responses\Api\ApiResponse;
use Wave8\Factotum\Cms\Contracts\Api\TermServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\Term\CreateTermDto;
use Wave8\Factotum\Cms\Dtos\Api\Term\UpdateTermDto;
use Wave8\Factotum\Cms\Http\Requests\Api\Term\CreateTermRequest;
use Wave8\Factotum\Cms\Http\Requests\Api\Term\SyncTermsRequest;
use Wave8\Factotum\Cms\Http\Requests\Api\Term\UpdateTermRequest;
use Wave8\Factotum\Cms\Models\Content;
use Wave8\Factotum\Cms\Models\ContentType;
use Wave8\Factotum\Cms\Models\Taxonomy;
use Wave8\Factotum\Cms\Models\Term;
use Wave8\Factotum\Cms\Resources\Api\TermResource;
use Wave8\Factotum\Cms\Services\Api\TermService;

final readonly class TermController
{
    public string $termResource;

    public function __construct(
        /** @var $termService TermService */
        private TermServiceInterface $termService,
    ) {
        $this->termResource = config('data_transfer.'.TermResource::class);
    }

    public function read(Taxonomy $taxonomy, Term $term): ApiResponse
    {
        if (! $taxonomy->terms()->where('id', $term->id)->exists()) {
            return ApiResponse::notFound();
        }

        return ApiResponse::make(
            data: $this->termResource::from($term)
        );
    }

    public function store(CreateTermRequest $request, Taxonomy $taxonomy): ApiResponse
    {
        $createTermDto = config('data_transfer.'.CreateTermDto::class);

        $term = $this->termService->createForTaxonomy(
            taxonomy: $taxonomy,
            data: $createTermDto::from($request),
        );

        return ApiResponse::make(
            data: $this->termResource::from($term),
            status: ApiResponse::HTTP_CREATED
        );
    }

    public function update(Taxonomy $taxonomy, Term $term, UpdateTermRequest $request): ApiResponse
    {
        if (! $taxonomy->terms()->where('id', $term->id)->exists()) {
            return ApiResponse::notFound();
        }

        $updateTermDto = config('data_transfer.'.UpdateTermDto::class);

        $this->termService->update(
            term: $term,
            data: $updateTermDto::from($request),
        );

        return ApiResponse::make(
            data: $this->termResource::from($term)
        );
    }

    public function destroy(Taxonomy $taxonomy, Term $term): ApiResponse
    {
        if (! $taxonomy->terms()->where('id', $term->id)->exists()) {
            return ApiResponse::notFound();
        }

        $this->termService->delete($term);

        return ApiResponse::noContent();
    }

    /**
     * Sync terms to a content entity.
     */
    public function syncToContent(ContentType $contentType, Content $content, SyncTermsRequest $request): ApiResponse
    {
        if (! $contentType->contents()->where('id', $content->id)->exists()) {
            return ApiResponse::notFound();
        }

        $this->termService->syncTermsToModel($content, $request->input('term_ids'));

        return ApiResponse::make(
            data: $this->termResource::collect($content->terms)
        );
    }

    /**
     * List terms attached to a content entity.
     */
    public function contentTerms(ContentType $contentType, Content $content): ApiResponse
    {
        if (! $contentType->contents()->where('id', $content->id)->exists()) {
            return ApiResponse::notFound();
        }

        return ApiResponse::make(
            data: $this->termResource::collect($content->terms)
        );
    }
}
