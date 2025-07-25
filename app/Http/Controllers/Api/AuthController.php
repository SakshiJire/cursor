<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'sometimes|in:admin,staff,student,parent'
        ]);

        $user = User::where('email', $request->email)
                   ->where('is_active', true)
                   ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check role if provided
        if ($request->role && $user->role !== $request->role) {
            throw ValidationException::withMessages([
                'role' => ['You do not have permission to login as this role.'],
            ]);
        }

        $token = $user->createToken('api-token', [$user->role])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user->load($this->getUserRelations($user->role)),
                'token' => $token,
                'role' => $user->role
            ]
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:15',
            'role' => 'required|in:admin,staff,student,parent'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $request->role,
            'is_active' => true
        ]);

        $token = $user->createToken('api-token', [$user->role])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'role' => $user->role
            ]
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load($this->getUserRelations($request->user()->role));

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:15',
            'avatar' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            'preferences' => 'sometimes|array'
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->update($request->only(['name', 'phone', 'preferences']));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user->fresh()->load($this->getUserRelations($user->role))
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    private function getUserRelations($role)
    {
        switch ($role) {
            case 'student':
                return ['student.class', 'student.academicYear', 'student.parents'];
            case 'parent':
                return ['parent.students.class'];
            case 'staff':
                return ['staff'];
            case 'admin':
                return [];
            default:
                return [];
        }
    }
}