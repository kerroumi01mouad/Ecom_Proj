<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display the authenticated user's profile.
     */
    public function show(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'User profile retrieved successfully.',
            'data' => $request->user(),
        ], 200);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'current_password' => [
                'required_with:password',
                'current_password',
            ],
            'password' => [
                'sometimes',
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        if (empty($validated)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide at least one field to update.',
            ], 422);
        }

        // Reset email verification if the email changed.
        if (
            isset($validated['email'])
            && $validated['email'] !== $user->email
        ) {
            $validated['email_verified_at'] = null;
        }

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Never pass current_password into the update payload.
        unset($validated['current_password']);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User profile updated successfully.',
            'data' => $user->fresh(),
        ], 200);
    }
}