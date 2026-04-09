<?php

namespace Wave8\Factotum\Cms\Observers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
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
        $colName = Str::lower(Str::snake($contentField->name));

        Schema::table($tableName, function (Blueprint $table) use ($contentField, $afterColumn, $colName) {
            $column = match ($contentField->type) {
                ContentFieldType::TEXT,
                ContentFieldType::TEXTAREA,
                ContentFieldType::URL,
                ContentFieldType::SELECT => $table->string($colName)->nullable(),

                ContentFieldType::IMAGE_UPLOAD => $table->unsignedBigInteger($colName)->nullable(),

                ContentFieldType::CHECKBOX => $table->boolean($colName),
                ContentFieldType::NUMBER => $table->integer($colName),
            };

            if ($contentField->type === ContentFieldType::IMAGE_UPLOAD) {
                $table->foreign($colName)->references('id')->on('media');
            }

            $column->after($afterColumn);
        });
    }

    /**
     * Get the last dynamic column of the table, excluding reserved columns.
     * Used to keep always timestamps at the end
     */
    private function getLastDynamicColumn(string $tableName): string
    {
        $reservedColumns = ['created_at', 'updated_at', 'deleted_at'];

        $columns = Schema::getColumnListing($tableName);
        $nonReserved = array_filter($columns, fn (string $col) => ! in_array($col, $reservedColumns));

        return end($nonReserved) ?: 'id';
    }
}
