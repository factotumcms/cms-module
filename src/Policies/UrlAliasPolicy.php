<?php

namespace Wave8\Factotum\Cms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Wave8\Factotum\Base\Contracts\Api\RoleServiceInterface;
use Wave8\Factotum\Base\Models\User;
use Wave8\Factotum\Cms\Models\UrlAlias;

class UrlAliasPolicy
{
    public function __construct(private RoleServiceInterface $roleService) {}

    use HandlesAuthorization;

    public function read(User $user, UrlAlias $urlAlias): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, UrlAlias $urlAlias): bool
    {
        return true;
    }

    public function delete(User $user, UrlAlias $urlAlias): bool
    {
        return true;
    }
}
