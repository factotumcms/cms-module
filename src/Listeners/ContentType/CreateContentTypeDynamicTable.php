<?php

namespace Wave8\Factotum\Cms\Listeners\ContentType;


use Illuminate\Contracts\Queue\ShouldQueue;
use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Events\ContentTypeCreated;
use Wave8\Factotum\Cms\Services\Api\ContentTypeService;

readonly class CreateContentTypeDynamicTable implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        /** @var ContentTypeService */
        private ContentTypeServiceInterface $contentTypeService,
    ) {}

    /**
     * Handle the event.
     */
    public function handle(ContentTypeCreated $event): void
    {
        $this->contentTypeService->generateDynamicTable($event->contentType);
    }
}
