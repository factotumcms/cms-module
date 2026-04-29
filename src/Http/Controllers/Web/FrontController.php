<?php

namespace Wave8\Factotum\Cms\Http\Controllers\Web;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Wave8\Factotum\Base\Enums\Locale;
use Wave8\Factotum\Base\Http\Responses\Api\ApiResponse;
use Wave8\Factotum\Cms\Contracts\Api\ContentServiceInterface;
use Wave8\Factotum\Cms\Contracts\Api\UrlAliasServiceInterface;
use Wave8\Factotum\Cms\Models\Content;
use Wave8\Factotum\Cms\Models\ContentType;
use Wave8\Factotum\Cms\Models\Term;
use Wave8\Factotum\Cms\Resources\Api\ContentResource;
use Wave8\Factotum\Cms\Resources\Api\ContentTypeResource;
use Wave8\Factotum\Cms\Resources\Api\TermResource;
use Wave8\Factotum\Cms\Resources\Api\UrlAliasResource;
use Wave8\Factotum\Cms\Services\Api\ContentService;
use Wave8\Factotum\Cms\Services\Api\UrlAliasService;

final readonly class FrontController
{
    public function __construct(
        /** @var $urlAliasService UrlAliasService */
        private UrlAliasServiceInterface $urlAliasService,

        /** @var $contentService ContentService */
        private ContentServiceInterface $contentService,
    ) {}

    /**
     * Resolve a URI path to its routable entity.
     * Returns the entity data, a 301 redirect, or a 404.
     */
    public function __invoke(string $path): ApiResponse|JsonResponse|RedirectResponse
    {
        $uri = '/'.trim($path, '/');
        $locale = app()->getLocale();

        $alias = $this->urlAliasService->resolve($uri, $locale);

        if (! $alias) {
            abort(404);
        }

        // Handle 301 redirect
        if ($alias->redirect_to) {
            return response()->json([
                'redirect' => true,
                'redirect_to' => $alias->redirect_to,
                'status' => 301,
            ], 301);
        }

        // Handle non-canonical: return canonical URL for the client
        if (! $alias->is_canonical) {
            $canonical = $alias->routable?->canonicalUrl();

            if ($canonical) {
                return response()->json([
                    'redirect' => true,
                    'redirect_to' => $canonical->uri,
                    'status' => 301,
                ], 301);
            }
        }

        // Resolve the routable entity
        $routable = $alias->routable;

        if (! $routable) {
            return ApiResponse::notFound();
        }

        switch(get_class($routable)) {
            case Content::class:
                $type = 'content';
                $entity = ContentResource::from($routable)->additional([
                    'fields' => $this->contentService->getDynamicFields($routable),
                ]);
                break;
            case Term::class:
                $type = 'term';
                $entity = TermResource::from($routable);
                break;
            case ContentType::class:
                $type = 'content_type';
                $entity = ContentTypeResource::from($routable);
                break;
            default:
                $type = 'unknown';
                $entity = null;
        }

        return ApiResponse::make(
            data: [
                'type' => $type,
                'alias' => UrlAliasResource::from($alias),
                'entity' => $entity
            ]
        );
    }
}

