<?php

namespace App\Policies;

use App\Models\FavoriteLaw;
use App\Models\User;

class FavoriteLawPolicy
{
    public function view(User $user, FavoriteLaw $favoriteLaw): bool
    {
        return $favoriteLaw->user_id === $user->id;
    }

    public function delete(User $user, FavoriteLaw $favoriteLaw): bool
    {
        return $favoriteLaw->user_id === $user->id;
    }
}
