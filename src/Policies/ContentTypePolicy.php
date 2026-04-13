<?php

namespace Wave8\Factotum\Cms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Wave8\Factotum\Base\Contracts\Api\RoleServiceInterface;
use Wave8\Factotum\Base\Models\User;
use Wave8\Factotum\Cms\Models\ContentType;

class ContentTypePolicy
{
    public function __construct(private RoleServiceInterface $roleService) {}

    use HandlesAuthorization;

    public function create(User $user): bool
    {
        return true;
    }

    public function createContentField(User $user): bool
    {
        return true;
    }

    public function read(User $user): bool
    {
        return true;
    }

    public function update(User $user, ContentType $contentType): bool
    {
        // Prevent the updates on production environment because is a critical object.
        // Follow always this pattern: updates on dev -> staging -> production
        return $contentType->editable && config('app.env') !== 'production';
    }

    public function delete(User $user): bool
    {
        return true;
    }
}
