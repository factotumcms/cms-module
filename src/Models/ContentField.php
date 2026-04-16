<?php

namespace Wave8\Factotum\Cms\Models;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wave8\Factotum\Cms\Enums\ContentFieldType;
use Wave8\Factotum\Cms\Policies\ContentFieldPolicy;
use Wave8\Factotum\Cms\Resources\Models\ContentField\CfConfigResource;

#[UsePolicy(ContentFieldPolicy::class)]
class ContentField extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'content_type_id',
        'name',
        'label',
        'type',
        'order_no',
        'configs',
    ];

    protected $casts = [
        'configs' => CfConfigResource::class,
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
