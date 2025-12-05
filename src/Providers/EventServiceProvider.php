<?php

namespace Wave8\Factotum\Cms\Providers;

use Illuminate\Events\EventServiceProvider as LaravelEventServiceProvider;
use Illuminate\Support\Facades\Event;
use Wave8\Factotum\Cms\Events\ContentTypeCreated;
use Wave8\Factotum\Cms\Listeners\ContentType\CreateContentTypeDynamicTable;

class EventServiceProvider extends LaravelEventServiceProvider
{
    public array $events = [
        ContentTypeCreated::class => CreateContentTypeDynamicTable::class,
    ];

    public function boot(): void
    {
        foreach ($this->events as $event => $listener) {
            Event::listen($event, $listener);
        }
    }
}
