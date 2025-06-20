<?php

namespace App\Policies;

use App\Models\User;

class EmployeePolicy
{
    public function hasAccess(User $user, $permission)
    {
        return $user->isAdmin() || $user->hasPermissionTo($permission);
    }

    /**
     * Determine whether the user can view any models.
     */
    public function view(User $user): bool
    {
        return $this->hasAccess($user, 'View Staff List');
    }

    public function create(User $user): bool
    {
        return $this->hasAccess($user, 'Create New Employee');
    }

    public function edit(User $user): bool
    {
        return $this->hasAccess($user, 'Edit Staff');
    }

    public function destroy(User $user): bool
    {
        return $this->hasAccess($user, 'Delete Employee');
    }
}
