<?php

namespace Wave8\Factotum\Cms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Wave8\Factotum\Base\Contracts\Api\RoleServiceInterface;
use Wave8\Factotum\Base\Models\User;
use Wave8\Factotum\Cms\Models\Translation;

class TranslationPolicy
{
    public function __construct(private RoleServiceInterface $roleService) {}

    use HandlesAuthorization;

    public function link(User $user): bool
    {
        return true;
    }

    public function unlink(User $user, Translation $translation): bool
    {
        return true;
    }

    public function read(User $user): bool
    {
        return true;
    }
}
