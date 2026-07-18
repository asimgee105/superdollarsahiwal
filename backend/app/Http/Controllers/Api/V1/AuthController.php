<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Models\Setting;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct(protected UserService $userService) {}

    /**
     * Handle user registration.
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = $this->userService->createUser($data);
        
        // Welcome Email
        try {
            Mail::send('emails.general', [
                'title' => 'Welcome to AURA Enterprise!',
                'greeting' => 'Hello ' . $user->name . ',',
                'message_text' => 'Thank you for creating an account on our platform. We are thrilled to welcome you to the AURA fashion family! Start exploring premium apparel, footwear, and accessories now.',
                'button_text' => 'Shop Collection',
                'button_url' => 'http://www.superdollarsahiwal.com/catalog',
            ], function ($message) use ($user) {
                $message->to($user->email)->subject('Welcome to AURA Enterprise!');
            });
        } catch (\Exception $e) {}

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
            'message' => 'User registered successfully.',
        ], 201);
    }

    /**
     * Handle user login.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        // Security Alert for Login
        try {
            Mail::send('emails.general', [
                'title' => 'New Login Detected',
                'greeting' => 'Hello ' . $user->name . ',',
                'message_text' => 'A new login session has been started on your AURA account. Details of the session are below.',
                'details' => [
                    'IP Address' => request()->ip(),
                    'Timestamp' => now()->toDateTimeString(),
                    'User Agent' => request()->userAgent(),
                ]
            ], function ($message) use ($user) {
                $message->to($user->email)->subject('AURA Security Alert: New Login Session');
            });
        } catch (\Exception $e) {}

        $token = $user->createToken('auth_token')->plainTextToken;
        $this->userService->logActivity('User logged in via Sanctum API', $user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
            'message' => 'User logged in successfully.',
        ]);
    }

    /**
     * Request a one-time password (OTP) sent to email.
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->email;
        $cacheKey = 'email_otp_' . md5($email);
        
        $otpLength = (int)Setting::get('otp_length', 6);
        $expiryMinutes = (int)Setting::get('otp_expiry_minutes', 10);
        $resendDelay = (int)Setting::get('otp_resend_delay_seconds', 60);

        // Check if resend block is active
        $existing = null;
        try {
            $existing = Cache::get($cacheKey);
        } catch (\Throwable $e) {
            Cache::forget($cacheKey);
        }

        if ($existing) {
            $resendAvailableAt = isset($existing['resend_available_at_timestamp']) 
                ? $existing['resend_available_at_timestamp'] 
                : (is_object($existing['resend_available_at'] ?? null) ? $existing['resend_available_at']->timestamp : time());

            if (time() < $resendAvailableAt) {
                $secondsLeft = $resendAvailableAt - time();
                return response()->json([
                    'success' => false,
                    'message' => "Please wait {$secondsLeft} seconds before requesting a new OTP code."
                ], 429);
            }
        }

        // Generate Code
        $code = '';
        for ($i = 0; $i < $otpLength; $i++) {
            $code .= rand(0, 9);
        }

        // Save to cache
        Cache::put($cacheKey, [
            'code' => $code,
            'attempts' => 0,
            'expires_at_timestamp' => now()->addMinutes($expiryMinutes)->timestamp,
            'resend_available_at_timestamp' => now()->addSeconds($resendDelay)->timestamp,
        ], now()->addMinutes($expiryMinutes));

        // Send dynamic mail
        try {
            Mail::send('emails.otp', [
                'title' => 'Email OTP Login & Registration',
                'description' => 'Use the secure numeric verification code below to login or register your customer profile on AURA Enterprise.',
                'code' => $code,
                'expiry' => $expiryMinutes,
            ], function ($message) use ($email) {
                $message->to($email)->subject('AURA Account Verification OTP');
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP email: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verification code has been sent successfully to ' . $email,
            'data' => [
                'resend_delay' => $resendDelay,
                'expiry_minutes' => $expiryMinutes,
            ]
        ]);
    }

    /**
     * Verify OTP code and register or login user.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string'],
        ]);

        $email = $request->email;
        $otp = $request->otp;
        $cacheKey = 'email_otp_' . md5($email);
        
        $otpData = null;
        try {
            $otpData = Cache::get($cacheKey);
        } catch (\Throwable $e) {
            Cache::forget($cacheKey);
        }

        if (!$otpData) {
            return response()->json([
                'success' => false,
                'message' => 'Your OTP verification code has expired or was not requested.'
            ], 422);
        }

        $maxRetries = (int)Setting::get('otp_max_retry_attempts', 5);
        if ($otpData['attempts'] >= $maxRetries) {
            Cache::forget($cacheKey);
            return response()->json([
                'success' => false,
                'message' => 'Maximum retry attempts reached. Please request a new OTP code.'
            ], 422);
        }

        if ($otpData['code'] !== $otp) {
            $otpData['attempts']++;
            $expiresAt = isset($otpData['expires_at_timestamp'])
                ? now()->createFromTimestamp($otpData['expires_at_timestamp'])
                : (is_object($otpData['expires_at'] ?? null) ? $otpData['expires_at'] : now()->addMinutes(10));

            Cache::put($cacheKey, $otpData, $expiresAt);
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP verification code.'
            ], 422);
        }

        // Clear OTP from Cache
        Cache::forget($cacheKey);

        // Find or create customer
        $user = User::where('email', $email)->first();

        if (!$user) {
            Cache::put('otp_verified_email_' . md5($email), true, now()->addMinutes(15));
            return response()->json([
                'success' => true,
                'data' => [
                    'is_new_user' => true,
                    'email' => $email,
                ],
                'message' => 'OTP verified successfully. Please fill your registration details to continue.',
            ]);
        }

        // Security Alert for Login
        try {
            Mail::send('emails.general', [
                'title' => 'OTP Login Success',
                'greeting' => 'Hello ' . $user->name . ',',
                'message_text' => 'A new login session has been verified using Email OTP. Details of the session are below.',
                'details' => [
                    'IP Address' => request()->ip(),
                    'Timestamp' => now()->toDateTimeString(),
                    'User Agent' => request()->userAgent(),
                ]
            ], function ($message) use ($user) {
                $message->to($user->email)->subject('AURA Security Alert: OTP Session Started');
            });
        } catch (\Exception $e) {}

        $token = $user->createToken('auth_token')->plainTextToken;
        $this->userService->logActivity('User logged in via Email OTP', $user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
                'is_new_user' => false,
            ],
            'message' => 'OTP verification succeeded.',
        ]);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        $this->userService->logActivity('User logged out from Sanctum API', $user->id);

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Handle requesting password reset OTP code.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user account found with that email address.'
            ], 404);
        }

        $otpLength = (int)Setting::get('otp_length', 6);
        $expiryMinutes = (int)Setting::get('otp_expiry_minutes', 10);
        $resendDelay = (int)Setting::get('otp_resend_delay_seconds', 60);

        $cacheKey = 'password_reset_otp_' . md5($email);
        
        $existing = null;
        try {
            $existing = Cache::get($cacheKey);
        } catch (\Throwable $e) {
            Cache::forget($cacheKey);
        }

        if ($existing) {
            $resendAvailableAt = isset($existing['resend_available_at_timestamp']) 
                ? $existing['resend_available_at_timestamp'] 
                : (is_object($existing['resend_available_at'] ?? null) ? $existing['resend_available_at']->timestamp : time());

            if (time() < $resendAvailableAt) {
                $secondsLeft = $resendAvailableAt - time();
                return response()->json([
                    'success' => false,
                    'message' => "Please wait {$secondsLeft} seconds before requesting a new reset OTP."
                ], 429);
            }
        }

        // Generate Code
        $code = '';
        for ($i = 0; $i < $otpLength; $i++) {
            $code .= rand(0, 9);
        }

        Cache::put($cacheKey, [
            'code' => $code,
            'attempts' => 0,
            'expires_at_timestamp' => now()->addMinutes($expiryMinutes)->timestamp,
            'resend_available_at_timestamp' => now()->addSeconds($resendDelay)->timestamp,
        ], now()->addMinutes($expiryMinutes));

        try {
            Mail::send('emails.otp', [
                'title' => 'Password Reset Verification',
                'description' => 'Use the secure OTP code below to reset your AURA account password.',
                'code' => $code,
                'expiry' => $expiryMinutes,
            ], function ($message) use ($email) {
                $message->to($email)->subject('AURA Password Reset Verification');
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reset email: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset OTP has been sent successfully to your email.',
            'data' => [
                'resend_delay' => $resendDelay,
                'expiry_minutes' => $expiryMinutes,
            ]
        ]);
    }

    /**
     * Handle resetting password using OTP code.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = $request->email;
        $otp = $request->otp;
        $password = $request->password;

        $cacheKey = 'password_reset_otp_' . md5($email);
        $otpData = null;
        try {
            $otpData = Cache::get($cacheKey);
        } catch (\Throwable $e) {
            Cache::forget($cacheKey);
        }

        if (!$otpData) {
            return response()->json([
                'success' => false,
                'message' => 'Your password reset OTP has expired or was not requested.'
            ], 422);
        }

        $maxRetries = (int)Setting::get('otp_max_retry_attempts', 5);
        if ($otpData['attempts'] >= $maxRetries) {
            Cache::forget($cacheKey);
            return response()->json([
                'success' => false,
                'message' => 'Maximum retry attempts reached. Please request a new OTP code.'
            ], 422);
        }

        if ($otpData['code'] !== $otp) {
            $otpData['attempts']++;
            $expiresAt = isset($otpData['expires_at_timestamp'])
                ? now()->createFromTimestamp($otpData['expires_at_timestamp'])
                : (is_object($otpData['expires_at'] ?? null) ? $otpData['expires_at'] : now()->addMinutes(10));

            Cache::put($cacheKey, $otpData, $expiresAt);
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP verification code.'
            ], 422);
        }

        // Clear OTP from Cache
        Cache::forget($cacheKey);

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found.'
            ], 404);
        }

        $user->password = Hash::make($password);
        $user->save();

        // Send confirmation email
        try {
            Mail::send('emails.general', [
                'title' => 'Password Changed Successfully',
                'greeting' => 'Hello ' . $user->name . ',',
                'message_text' => 'The password for your AURA Enterprise account was successfully changed. If you did not make this change, please contact our support team immediately.',
            ], function ($message) use ($user) {
                $message->to($user->email)->subject('AURA Security Alert: Password Changed');
            });
        } catch (\Exception $e) {}

        $this->userService->logActivity('User reset password using Email OTP', $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Your password has been reset successfully. Please log in with your new password.',
        ]);
    }

    /**
     * Retrieve the authenticated user profile.
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user()->load(['roles', 'profile'])),
            'message' => 'Profile retrieved successfully.',
        ]);
    }

    /**
     * Check if a user exists in the system by email.
     */
    public function checkUser(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        return response()->json([
            'success' => true,
            'exists' => $user !== null,
        ]);
    }

    /**
     * Complete new user registration with Name, Phone, and Password.
     */
    public function registerComplete(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = $request->email;
        $cacheKey = 'otp_verified_email_' . md5($email);

        if (!Cache::has($cacheKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired registration session. Please verify your email via OTP again.'
            ], 422);
        }

        $user = $this->userService->createUser([
            'name' => $request->name,
            'email' => $email,
            'password' => $request->password,
        ]);

        $customerRole = DB::table('roles')->where('name', 'Customer')->first();
        if ($customerRole) {
            DB::table('model_has_roles')->insert([
                'role_id' => $customerRole->id,
                'model_type' => 'App\Models\User',
                'model_id' => $user->id,
            ]);
        }

        if ($user->profile) {
            $user->profile->update([
                'phone' => $request->phone,
            ]);
        }

        Cache::forget($cacheKey);

        try {
            Mail::send('emails.general', [
                'title' => 'Welcome to AURA Enterprise!',
                'greeting' => 'Hello ' . $user->name . ',',
                'message_text' => 'Your account has been successfully verified and created. We are thrilled to welcome you to the AURA fashion family!',
                'button_text' => 'Shop Collection',
                'button_url' => 'http://www.superdollarsahiwal.com/catalog',
            ], function ($message) use ($user) {
                $message->to($user->email)->subject('Welcome to AURA Enterprise!');
            });
        } catch (\Exception $e) {}

        $token = $user->createToken('auth_token')->plainTextToken;
        $this->userService->logActivity('New user completed signup profile details', $user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
            'message' => 'Registration completed successfully.',
        ]);
    }
}
