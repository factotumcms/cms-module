<?php

namespace Wave8\Factotum\Cms\Http\Controllers\Api;

use Wave8\Factotum\Base\Http\Responses\Api\ApiResponse;
use Wave8\Factotum\Cms\Contracts\Api\UrlAliasServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\UrlAlias\CreateUrlAliasDto;
use Wave8\Factotum\Cms\Dtos\Api\UrlAlias\UpdateUrlAliasDto;
use Wave8\Factotum\Cms\Http\Requests\Api\UrlAlias\CreateUrlAliasRequest;
use Wave8\Factotum\Cms\Http\Requests\Api\UrlAlias\UpdateUrlAliasRequest;
use Wave8\Factotum\Cms\Models\Content;
use Wave8\Factotum\Cms\Models\ContentType;
use Wave8\Factotum\Cms\Models\Term;
use Wave8\Factotum\Cms\Models\UrlAlias;
use Wave8\Factotum\Cms\Resources\Api\UrlAliasResource;
use Wave8\Factotum\Cms\Services\Api\UrlAliasService;

final readonly class UrlAliasController
{
    private const array ROUTABLE_MAP = [
        'content' => Content::class,
        'term' => Term::class,
        'content_type' => ContentType::class,
    ];

    public string $urlAliasResource;

    public function __construct(
        /** @var $urlAliasService UrlAliasService */
        private UrlAliasServiceInterface $urlAliasService,
    ) {
        $this->urlAliasResource = config('data_transfer.'.UrlAliasResource::class);
    }

    /**
     * Create a new URL alias manually.
     */
    public function store(CreateUrlAliasRequest $request): ApiResponse
    {
        $createUrlAliasDto = config('data_transfer.'.CreateUrlAliasDto::class);
        $dto = $createUrlAliasDto::from($request);

        $fqcn = self::ROUTABLE_MAP[$dto->routableType] ?? null;

        if (! $fqcn) {
            return ApiResponse::error('Unknown routable type.', ApiResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $routable = $fqcn::findOrFail($dto->routableId);

        $alias = $this->urlAliasService->createForRoutable(
            routable: $routable,
            uri: $dto->uri,
            locale: $dto->locale,
            isCanonical: $dto->isCanonical,
        );

        return ApiResponse::make(
            data: $this->urlAliasResource::from($alias),
            status: ApiResponse::HTTP_CREATED
        );
    }

    /**
     * Read a single URL alias.
     */
    public function read(UrlAlias $urlAlias): ApiResponse
    {
        return ApiResponse::make(
            data: $this->urlAliasResource::from($urlAlias)
        );
    }

    /**
     * Update an existing URL alias.
     */
    public function update(UrlAlias $urlAlias, UpdateUrlAliasRequest $request): ApiResponse
    {
        $updateUrlAliasDto = config('data_transfer.'.UpdateUrlAliasDto::class);
        $dto = $updateUrlAliasDto::from($request);

        $urlAlias->update($dto->toArray());

        return ApiResponse::make(
            data: $this->urlAliasResource::from($urlAlias->fresh())
        );
    }

    /**
     * Delete a URL alias.
     */
    public function destroy(UrlAlias $urlAlias): ApiResponse
    {
        $this->urlAliasService->delete($urlAlias);

        return ApiResponse::noContent();
    }

    /**
     * Get all aliases for a routable entity.
     */
    public function forRoutable(string $type, int $id): ApiResponse
    {
        $fqcn = self::ROUTABLE_MAP[$type] ?? null;

        if (! $fqcn) {
            return ApiResponse::error('Unknown routable type.', ApiResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $routable = $fqcn::findOrFail($id);
        $aliases = $this->urlAliasService->getForRoutable($routable);

        return ApiResponse::make(
            data: $this->urlAliasResource::collect($aliases)
        );
    }
}

