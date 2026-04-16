<?php

namespace Wave8\Factotum\Cms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Wave8\Factotum\Base\Contracts\Api\RoleServiceInterface;
use Wave8\Factotum\Base\Models\User;
use Wave8\Factotum\Cms\Models\Content;

class ContentPolicy
{
    public function __construct(private RoleServiceInterface $roleService) {}

    use HandlesAuthorization;

    public function create(User $user): bool
    {
        return true;
    }

    public function read(User $user): bool
    {
        return true;
    }

    public function update(User $user, Content $content): bool
    {
        return true;
    }

    public function delete(User $user, Content $content): bool
    {
        return config('app.env') !== 'production';
    }
}
