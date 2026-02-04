<?php

namespace App\Policies;

use App\Models\Flux;
use App\Models\User;

class FluxPolicy
{
    public function view(User $user, Flux $flux): bool
    {
        return $user->tenant_id === $flux->tenant_id;
    }

    public function update(User $user, Flux $flux): bool
    {
        return $user->tenant_id === $flux->tenant_id;
    }

    public function delete(User $user, Flux $flux): bool
    {
        return $user->tenant_id === $flux->tenant_id;
    }
}
