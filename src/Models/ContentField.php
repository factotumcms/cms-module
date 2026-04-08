<?php

namespace Wave8\Factotum\Cms\Models;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Wave8\Factotum\Cms\Enums\ContentFieldType;
use Wave8\Factotum\Cms\Policies\ContentFieldPolicy;

#[UsePolicy(ContentFieldPolicy::class)]
class ContentField extends Model
{
    protected $fillable = [
        'content_type_id',
        'name',
        'label',
        'type',
        'old_type',
        'order_no',
        'mandatory',
        'readonly',
        'hint',
        'options',

        'allowed_types',

        'max_file_size',
        'min_width_size',
        'min_height_size',

        'image_operation',
        'image_bw',
        'resizes',

        'linked_content_type_id',

        'visibility_rules',
        'mandatory_rules',
    ];

    protected $casts = [
        'mandatory' => 'boolean',
        'readonly' => 'boolean',
        'options' => 'array',
        'image_bw' => 'boolean',
        'allowed_types' => 'array',
        'resizes' => 'array',
        'visibility_rules' => 'array',
        'mandatory_rules' => 'array',
        'type' => ContentFieldType::class,
    ];

    protected $searchable = [
        'label',
        'name',
    ];

    public function contentType(): BelongsTo
    {
        return $this->belongsTo(ContentType::class);
    }
}
