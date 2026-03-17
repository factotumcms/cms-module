<?php

namespace Wave8\Factotum\Cms\Observers;

use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Models\ContentType;
use Wave8\Factotum\Cms\Services\Api\ContentTypeService;

class ContentTypeObserver
{
    public function __construct(
        /** @var ContentTypeService $contentTypeService */
        private ContentTypeServiceInterface $contentTypeService,
    ) {}

    /**
     * Handle the ContentType "created" event.
     *
     * @throws \Exception
     */
    public function created(ContentType $contentType): void
    {
        $this->contentTypeService->generateDynamicTableAndModel($contentType);
    }

    /**
     * Handle the ContentType "updated" event.
     */
    public function updated(ContentType $contentType): void
    {
        //
    }

    /**
     * Handle the ContentType "deleted" event.
     */
    public function deleted(ContentType $contentType): void
    {
        //
    }

    /**
     * Handle the ContentType "restored" event.
     */
    public function restored(ContentType $contentType): void
    {
        //
    }

    /**
     * Handle the ContentType "force deleted" event.
     */
    public function forceDeleted(ContentType $contentType): void
    {
        //
    }
}
