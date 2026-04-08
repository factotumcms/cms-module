<?php

namespace Wave8\Factotum\Cms\Observers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Wave8\Factotum\Cms\Models\ContentType;

class ContentTypeObserver
{
    public function __construct(
        private readonly Filesystem $fs
    ) {}

    /**
     * Handle the ContentType "created" event.
     *
     * @throws \Exception
     */
    public function created(ContentType $contentType): void
    {
        $this->createDynamicTable($contentType->type);
        $this->createDynamicModel($contentType->type);
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

    /**
     * @throws \Exception
     */
    private function createDynamicTable(string $type): void
    {
        $tableName = Str::lower(Str::snake($type));

        try {
            // Dynamic table creation
            if (! Schema::hasTable($tableName)) {
                Schema::create($tableName, function (Blueprint $table) {
                    $table->increments('id');

                    $table->foreignId('content_type_id')->cascadeOnDelete();
                    $table->foreignId('content_id')->cascadeOnDelete();
                });
            }
        } catch (\Exception $e) {
            // Dropping table
            Schema::dropIfExists($tableName);

            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    private function createDynamicModel(string $type): void
    {
        try {
            $tableName = Str::lower(Str::snake($type));
            $modelName = Str::ucfirst(Str::pascal($type));
            $modelPath = app_path('/Models');

            $modelFullPath = "{$modelPath}/{$modelName}.php";

            $modelDirectoryCreated = false;
            $modelCreated = false;

            // Dynamic model creation
            if (file_exists($modelFullPath)) {
                return;
            }

            File::ensureDirectoryExists($modelPath);
            $modelDirectoryCreated = true;

            if ($this->fs->copy(__DIR__.'/../../stubs/app/Models/DynamicModel.php.stub', $modelFullPath)) {
                $modelCreated = true;
            }

            $this->fs->replaceInFile('DynamicModel', $modelName, $modelFullPath);
            $this->fs->replaceInFile('dynamic_model', $tableName, $modelFullPath);
        } catch (\Exception $e) {
            // Manual rollback
            if ($modelCreated && file_exists($modelFullPath)) {
                File::delete($modelFullPath);
            }

            if ($modelDirectoryCreated && File::isDirectory($modelPath) && File::isEmptyDirectory($modelPath)) {
                File::deleteDirectory($modelPath);
            }

            throw $e;
        }
    }
}
