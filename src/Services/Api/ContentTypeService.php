<?php

namespace Wave8\Factotum\Cms\Services\Api;

use AllowDynamicProperties;
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

#[AllowDynamicProperties]
class ContentTypeService implements ContentTypeServiceInterface
{
    public function __construct(public readonly ContentType $model) {
        $this->fs = app(Filesystem::class);
    }

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
    public function generateDynamicTable(ContentType $contentType): void
    {
        $tableName = Str::lower(Str::snake($contentType->type));

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

    public function generateDynamicModel(ContentType $contentType): void
    {
        try {
            $tableName = Str::lower(Str::snake($contentType->type));
            $modelName = Str::ucfirst(Str::pascal($contentType->type));
            $modelPath = app_path('/Models');

            $modelFullPath = "{$modelPath}/{$modelName}.php";

            $modelDirectoryCreated = false;
            $modelCreated = false;

            // Dynamic model creation
            if (file_exists($modelFullPath)) {
                throw new \Exception("Model {$modelName} already exists!");
            }

            File::ensureDirectoryExists($modelPath);
            $modelDirectoryCreated = true;

            if ($this->fs->copy(__DIR__.'/../../../stubs/app/Models/DynamicModel.php.stub', $modelFullPath)) {
                $modelCreated = true;
            }

            $this->fs->replaceInFile('DynamicModel', $modelName, $modelFullPath);
            $this->fs->replaceInFile('dynamic_model', $tableName, $modelFullPath);
        }catch (\Exception $e) {
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
