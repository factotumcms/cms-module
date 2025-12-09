<?php

namespace Wave8\Factotum\Cms\Providers;

use Illuminate\Events\EventServiceProvider as LaravelEventServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends LaravelEventServiceProvider
{
    public array $events = [
    ];

    public function boot(): void
    {
        foreach ($this->events as $event => $listener) {
            Event::listen($event, $listener);
        }
    }
}
