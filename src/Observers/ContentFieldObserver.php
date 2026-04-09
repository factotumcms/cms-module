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

    private function addColumnsOnDynamicTable(ContentField $contentField): void
    {
        $tableName = $contentField->contentType->type;
        $afterColumn = $this->getLastDynamicColumn($tableName);

        Schema::table($tableName, function (Blueprint $table) use ($contentField, $afterColumn) {

            $column = match ($contentField->type) {
                ContentFieldType::TEXT,
                ContentFieldType::TEXTAREA,
                ContentFieldType::URL,
                ContentFieldType::SELECT => $table->string($contentField->name)->nullable(),

                ContentFieldType::IMAGE_UPLOAD => $table->foreignIdFor(Media::class, $contentField->name)
                    ->nullable()
                    ->constrained(),
                ContentFieldType::CHECKBOX => $table->boolean($contentField->name),
                ContentFieldType::NUMBER => $table->integer($contentField->name),
            };

            $column->after($afterColumn);
        });
    }


    private function getLastDynamicColumn(string $tableName): string
    {
        $reservedColumns = ['created_at', 'updated_at', 'deleted_at'];

        $columns = Schema::getColumnListing($tableName);

        // Rimuovi le colonne riservate (i timestamp in fondo)
        $nonReserved = array_filter($columns, fn (string $col) => !in_array($col, $reservedColumns));

        // L'ultima colonna non-riservata è quella dopo cui inserire
        return end($nonReserved) ?: 'id';
    }

}
