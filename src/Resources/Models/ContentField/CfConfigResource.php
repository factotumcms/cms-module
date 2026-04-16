<?php

namespace Wave8\Factotum\Cms\Resources\Models\ContentField;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
class CfConfigResource extends Resource
{
    public function __construct(
        public CfSelectConfigResource|CfImageUploadConfigResource|null $cfParams = null,
        public ?array $visibilityRules = [],
        public ?array $mandatoryRules = [],
        public bool $readonly = false,
        public bool $mandatory = false,
        public ?string $hint = null,
    ) {}

    // Please note:: overridden from method because when casting ContentField->config->cfParams, laravel doesn't know which type to cast to, so we need to handle it manually here.
    // cfParams is a different object based on the type of the content field, so we need to check the presence of options key to determine which type it is and cast accordingly.
    public static function from(mixed ...$payloads): static
    {
        $data = $payloads[0] ?? [];

        if (is_array($data)) {
            $params = $data['cf_params'] ?? $data['cfParams'] ?? null;

            if (is_array($params)) {
                $key = array_key_exists('cf_params', $data) ? 'cf_params' : 'cfParams';

                // todo:: Handle different cases when needed
                if (array_key_exists('options', $params)) {
                    $data[$key] = CfSelectConfigResource::from($params);
                } else {
                    $data[$key] = CfImageUploadConfigResource::from($params);
                }
            }

            return parent::from($data);
        }

        return parent::from($data);
    }
}
