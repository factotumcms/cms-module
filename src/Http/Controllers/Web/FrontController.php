<?php

namespace Wave8\Factotum\Cms\Http\Controllers\Web;

use Wave8\Factotum\Base\Http\Responses\Api\ApiResponse;
use Wave8\Factotum\Cms\Contracts\Api\ContentServiceInterface;
use Wave8\Factotum\Cms\Contracts\Api\UrlAliasServiceInterface;
use Wave8\Factotum\Cms\Enums\BaseContentType;
use Wave8\Factotum\Cms\Enums\PageOperation;
use Wave8\Factotum\Cms\Models\Content;
use Wave8\Factotum\Cms\Models\ContentType;
use Wave8\Factotum\Cms\Models\Term;
use Wave8\Factotum\Cms\Models\UrlAlias;
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
    public function __invoke(string $path): mixed
    {
        $uri = '/'.trim($path, '/');

        $alias = $this->urlAliasService->resolve($uri);

        if (! $alias) {
            abort(404);
        }

        // Handle 301 redirect
        if ($alias->redirect_to) {
            return redirect($alias->redirect_to, 301);
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

        switch (get_class($routable)) {
            case Content::class:
                return $this->handleContentClass($alias);
            case Term::class:
                // todo:: capire come si vuole gestire
                $entity = TermResource::from($routable);
                break;
            case ContentType::class:
                // todo:: capire come si vuole gestire
                $entity = ContentTypeResource::from($routable);
                break;
        }

        return ApiResponse::make(
            data: [
                'type' => get_class($routable),
                'alias' => UrlAliasResource::from($alias),
                'entity' => $entity ?? null,
            ]
        );
    }

    public function handleContentClass(UrlAlias $alias): mixed
    {
        $fields = $this->contentService->getDynamicFields($alias->routable);

        if ($alias->routable->contentType->type == BaseContentType::PAGES->value) {
            switch ($fields['page_operation']) {
                case PageOperation::SHOW_CONTENT->value:
                    $viewName = $fields['page_template'] ?: 'basic';

                    // Prima cerca nella app principale, poi fallback al package
                    $template = view()->exists($viewName)
                        ? $viewName
                        : 'factotum_cms::'.$viewName;

                    return response()->view($template, ['page' => $alias->routable, 'fields' => $fields]);
                case PageOperation::CONTENT_LIST->value:
                    return response()->view('factotum_cms::content-list', ['page' => $alias->routable, 'fields' => $fields]);
            }
        }

        if ($alias->routable->contentType->type == BaseContentType::NEWS->value) {
            return response()->view('factotum_cms::news', ['page' => $alias->routable, 'fields' => $fields]);
        }

        return null;
    }
}
