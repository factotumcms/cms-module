<?php

namespace Wave8\Factotum\Cms\Observers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Wave8\Factotum\Base\Models\Media;
use Wave8\Factotum\Cms\Enums\ContentFieldType;
use Wave8\Factotum\Cms\Models\ContentField;

class ContentFieldObserver
{
    public function __construct(
    ) {}

    /**
     * Handle the ContentType "created" event.
     *
     * @throws \Exception
     */
    public function created(ContentField $contentField): void
    {
        $this->addColumnsOnDynamicTable($contentField);
    }

    /**
     * Handle the ContentType "updated" event.
     */
    public function updated(ContentField $contentField): void
    {
        //
    }

    /**
     * Handle the ContentType "deleted" event.
     */
    public function deleted(ContentField $contentField): void
    {
        //
    }

    /**
     * Handle the ContentType "restored" event.
     */
    public function restored(ContentField $contentField): void
    {
        //
    }

    /**
     * Handle the ContentType "force deleted" event.
     */
    public function forceDeleted(ContentField $contentField): void
    {
        //
    }

    private function addColumnsOnDynamicTable(ContentField $contentField)
    {
        $tableName = $contentField->contentType->type;

        Schema::table($tableName, function (Blueprint $table) use ($contentField) {
            switch ($contentField->type) {
                case ContentFieldType::TEXT:
                case ContentFieldType::TEXTAREA:
                case ContentFieldType::SELECT:
                    $table->string($contentField->name)->nullable();
                    break;
                case ContentFieldType::IMAGE_UPLOAD:
                    $table->foreignIdFor(Media::class, $contentField->name)->nullable()->constrained();
            }
        });
    }
}
