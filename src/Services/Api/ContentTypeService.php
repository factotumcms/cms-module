<?php

namespace Wave8\Factotum\Cms\Services\Api;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\ContentField\CreateContentFieldDto;
use Wave8\Factotum\Cms\Dtos\Api\ContentType\CreateContentTypeDto;
use Wave8\Factotum\Cms\Enums\BaseContentType as ContentTypeEnum;
use Wave8\Factotum\Cms\Models\ContentField;
use Wave8\Factotum\Cms\Models\ContentType;

class ContentTypeService implements ContentTypeServiceInterface
{
    public function __construct(public readonly ContentType $model) {}

    public function single(int $id): ContentType
    {
        return $this->model::findOrFail($id);
    }

    public function create(CreateContentTypeDto $data): ContentType
    {
        $contentType = $this->model::create($data->toArray());

        return $contentType;
    }

    public function getByType(ContentTypeEnum|string $type): ContentType
    {
        return $this->model::where('type', $type)->firstOrFail();
    }

    public function createFieldForContentType(ContentType $contentType, CreateContentFieldDto $data): ContentField
    {
        return $contentType->content_fields()->create(
            $data->toArray()
        );
    }

    /**
     * @throws \Exception
     */
    public function generateDynamicTableAndModel(ContentType $contentType): void
    {
        $filesystem = app(Filesystem::class);

        $tableName = Str::lower(Str::snake($contentType->type));
        $modelName = Str::ucfirst(Str::pascal($contentType->type));
        $modelPath = base_path('/dynamics/models');
        $modelFullPath = "{$modelPath}/{$modelName}.php";
        $loader = require base_path('vendor/autoload.php');

        $modelDirectoryCreated = false;
        $modelCreated = false;

        try {
            // Dynamic table creation
            if (! Schema::hasTable($tableName)) {
                Schema::create($tableName, function (Blueprint $table) {
                    $table->increments('id');

                    $table->foreignId('content_type_id')->cascadeOnDelete();
                    $table->foreignId('content_id')->cascadeOnDelete();
                });
            }

            // Dynamic model creation
            //            if (file_exists($modelFullPath)) {
            //                throw new \Exception("Model {$modelName} already exists!");
            //            }

            File::ensureDirectoryExists($modelPath);
            $modelDirectoryCreated = true;

            if ($filesystem->copy(__DIR__.'/../../../stubs/app/Models/DynamicModel.php', $modelFullPath)) {
                $modelCreated = true;
            }

            $filesystem->replaceInFile('DynamicModel', $modelName, $modelFullPath);
            $filesystem->replaceInFile('dynamic_model', $tableName, $modelFullPath);

            $loader->addClassMap([
                'Wave8\\Factotum\\Cms\\Dynamics\\Models\\'.$modelName => $modelFullPath,
            ]);
        } catch (\Exception $e) {
            // Dropping table
            Schema::dropIfExists($tableName);

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
