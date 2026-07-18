<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    /**
     * Get all users with roles.
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query()->with('roles');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        if (isset($filters['trashed']) && $filters['trashed'] === 'only') {
            $query->onlyTrashed();
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Find user by ID.
     */
    public function find(int $id, bool $withTrashed = false): ?User
    {
        $query = User::query()->with(['roles', 'profile', 'addresses.city.state.country']);

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->find($id);
    }

    /**
     * Create user.
     */
    public function create(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        if (! empty($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }

        // Initialize profile
        $user->profile()->create();

        return $user;
    }

    /**
     * Update user.
     */
    public function update(User $user, array $data): User
    {
        $user->update(array_filter([
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'password' => ! empty($data['password']) ? $data['password'] : null,
        ]));

        if (isset($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }

        return $user;
    }

    /**
     * Delete user.
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Restore trashed user.
     */
    public function restore(int $id): bool
    {
        $user = User::onlyTrashed()->find($id);

        return $user ? $user->restore() : false;
    }
}
