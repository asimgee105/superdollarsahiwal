<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserRepository $userRepository,
        protected UserService $userService
    ) {}

    /**
     * Display a listing of users.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'role', 'trashed']);
        $users = $this->userRepository->getAll($filters);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'total' => $users->total(),
            ],
            'message' => 'Users retrieved successfully.',
        ]);
    }

    /**
     * Display the specified user details.
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id, true);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
            'message' => 'User details retrieved successfully.',
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'roles' => ['nullable', 'array'],
        ]);

        $user = $this->userService->createUser($data);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
            'message' => 'User account created successfully.',
        ], 201);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8'],
            'roles' => ['nullable', 'array'],
        ]);

        $updatedUser = $this->userService->updateUser($user, $data);

        return response()->json([
            'success' => true,
            'data' => new UserResource($updatedUser),
            'message' => 'User account updated successfully.',
        ]);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $this->userService->deleteUser($user);

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'User account deactivated (soft-deleted) successfully.',
        ]);
    }

    /**
     * Restore the specified soft-deleted user.
     */
    public function restore(int $id): JsonResponse
    {
        $restored = $this->userService->restoreUser($id);

        if (! $restored) {
            return response()->json([
                'success' => false,
                'message' => 'User not found or already active.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'User account restored successfully.',
        ]);
    }
}
