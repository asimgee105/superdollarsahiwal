<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index(): JsonResponse
    {
        $permissions = Permission::all();

        return response()->json([
            'success' => true,
            'data' => $permissions,
            'message' => 'Permissions retrieved successfully.',
        ]);
    }
}
