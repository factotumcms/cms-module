<?php

namespace Wave8\Factotum\Cms\Services\Api;

use Illuminate\Support\Str;
use Wave8\Factotum\Cms\Contracts\Api\ContentServiceInterface;
use Wave8\Factotum\Cms\Dtos\Api\Content\CreateContentDto;
use Wave8\Factotum\Cms\Dtos\Api\Content\UpdateContentDto;
use Wave8\Factotum\Cms\Models\Content;
use Wave8\Factotum\Cms\Models\ContentType;

readonly class ContentService implements ContentServiceInterface
{
    public function __construct(public Content $model) {}

    public function single(int $id): Content
    {
        return $this->model::findOrFail($id);
    }

    public function createContentForContentType(ContentType $contentType, CreateContentDto $data): Content
    {
        $contentFields = $data->fields;
        unset($data->fields);

        $content = $contentType->contents()->create($data->toArray());

        if (! empty($contentFields)) {
            $this->updateDynamicFields($content, $contentFields);
        }

        return $content;
    }

    public function update(Content $content, UpdateContentDto $data): Content
    {
        $content->update($data->toArray());

        if (! empty($data->fields)) {
            $this->updateDynamicFields($content, $data->fields);
        }

        return $content;
    }

    public function delete(Content $content): bool
    {
        $modelName = Str::ucfirst(Str::pascal($content->contentType->type));
        $fqcn = "App\\Models\\{$modelName}";

        if (! class_exists($fqcn)) {
            throw new \RuntimeException("Model {$fqcn} not found");
        }

        $fqcn::where('content_id', $content->id)->delete();

        return $content->delete();
    }

    public function getDynamicFields(Content $content): array
    {
        $fieldList = $content->contentType->contentFields->pluck('name')->toArray();

        $modelName = Str::ucfirst(Str::pascal($content->contentType->type));
        $fqcn = "App\\Models\\{$modelName}";

        if (! class_exists($fqcn)) {
            throw new \RuntimeException("Model {$fqcn} not found");
        }

        $records = $fqcn::select($fieldList)->where('content_id', $content->id)->first();

        return $records?->toArray() ?? [];
    }

    public function updateDynamicFields(Content $content, array $data): void
    {
        // Update dynamic fields
        $modelName = Str::ucfirst(Str::pascal($content->contentType->type));
        $fqcn = "App\\Models\\{$modelName}";

        if (! class_exists($fqcn)) {
            throw new \RuntimeException("Model {$fqcn} not found");
        }

        $record = $fqcn::where('content_id', $content->id)->firstOrCreate(['content_id' => $content->id]);
        foreach ($data as $fieldKey => $fieldValue) {
            $record->{$fieldKey} = $fieldValue;
        }

        $record->save();
    }
}
