<?php

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Enums\BaseContentType;
use Wave8\Factotum\Cms\Models\ContentType;

describe('ContentType model', function () {
    it('checks default content types exist after seeding', function () {
        $service = app(ContentTypeServiceInterface::class);

        $pages = $service->getByType(BaseContentType::PAGES);
        $news = $service->getByType(BaseContentType::NEWS);

        expect($pages)->toBeInstanceOf(ContentType::class)
            ->and($pages->type)->toBe(BaseContentType::PAGES->value)
            ->and($pages->label)->toBe('Pagine')
            ->and($pages->hierarchical)->toBeTrue()
            ->and($news)->toBeInstanceOf(ContentType::class)
            ->and($news->type)->toBe(BaseContentType::NEWS->value)
            ->and($news->label)->toBe('News')
            ->and($news->hierarchical)->toBeFalse();
    });

    it('checks ContentType model fillable properties', function () {
        $service = app(ContentTypeServiceInterface::class);
        $contentType = $service->getByType(BaseContentType::PAGES);

        $fillable = [
            'type',
            'editable',
            'order_no',
            'icon',
            'sitemap',
            'label',
            'visible',
            'hierarchical',
        ];

        expect($contentType->getFillable())->toEqual($fillable);
    });

    it('checks ContentType model relations', function () {
        $service = app(ContentTypeServiceInterface::class);
        $contentType = $service->getByType(BaseContentType::PAGES);

        expect($contentType->contentFields())->toBeInstanceOf(HasMany::class)
            ->and($contentType->contents())->toBeInstanceOf(HasMany::class)
            ->and($contentType->taxonomies())->toBeInstanceOf(BelongsToMany::class);
    });
});
