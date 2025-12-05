<?php

namespace Wave8\Factotum\Cms\Services\Api;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Wave8\Factotum\Cms\Contracts\Api\ContentTypeServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\ContentField\CreateContentFieldDto;
use Wave8\Factotum\Cms\Dtos\Api\ContentType\CreateContentTypeDto;
use Wave8\Factotum\Cms\Enums\ContentType as ContentTypeEnum;
use Wave8\Factotum\Cms\Events\ContentTypeCreated;
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

        ContentTypeCreated::dispatch($contentType);

        return $contentType;
    }

    public function getByType(ContentTypeEnum $type): ContentType
    {
        return $this->model::where('type', $type)->firstOrFail();
    }

    public function createFieldForContentType(ContentType $contentType, CreateContentFieldDto $data): ContentField
    {
        return $contentType->content_fields()->create(
            $data->toArray()
        );
    }

    public function generateDynamicTable(ContentType $contentType)
    {
        Schema::create($contentType->type, function (Blueprint $table) {
            $table->increments('id');

            $table->foreignId('content_type_id')->cascadeOnDelete();
            $table->foreignId('content_id')->cascadeOnDelete();
        });
    }
}
