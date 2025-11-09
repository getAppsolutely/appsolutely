<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class UserRepository extends BaseRepository
{
    public function model(): string
    {
        return User::class;
    }

    /**
     * Search users by name or email.
     */
    public function search(string $term): Builder
    {
        return $this->model->where(function (Builder $query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
        });
    }
}
