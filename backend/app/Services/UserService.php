<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

class UserService
{
    public function __construct(protected UserRepository $userRepository) {}

    /**
     * Log user activity.
     */
    public function logActivity(string $description, ?int $userId = null): void
    {
        ActivityLog::create([
            'user_id' => $userId ?? Auth::id(),
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Create administrative user.
     */
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $user = $this->userRepository->create($data);

        $this->logActivity("User account created: {$user->email} (ID: {$user->id})");

        return $user;
    }

    /**
     * Update user account details.
     */
    public function updateUser(User $user, array $data): User
    {
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $updatedUser = $this->userRepository->update($user, $data);
        $this->logActivity("User account updated: {$updatedUser->email} (ID: {$updatedUser->id})");

        return $updatedUser;
    }

    /**
     * Deactivate user account (set soft delete).
     */
    public function deleteUser(User $user): bool
    {
        $userId = $user->id;
        $userEmail = $user->email;
        $deleted = $this->userRepository->delete($user);

        if ($deleted) {
            $this->logActivity("User account deactivated: {$userEmail} (ID: {$userId})");
        }

        return $deleted;
    }

    /**
     * Restore deactivated user.
     */
    public function restoreUser(int $id): bool
    {
        $restored = $this->userRepository->restore($id);
        if ($restored) {
            $this->logActivity("User account restored: ID {$id}");
        }

        return $restored;
    }
}
