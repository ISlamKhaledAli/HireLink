<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\Models\User;


class ProfileController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'linkedin_id' => ['nullable', 'string', 'max:255'],
            'resume' => ['nullable', 'file', 'mimes:pdf', 'max:2048'], // PDF Only, Max 2MB
        ]);

        if ($request->hasFile('resume')) {
            if ($user->resume_path) {
                Storage::disk('public')->delete($user->resume_path);
            }

            $path = $request->file('resume')->store('resumes', 'public');
            $validated['resume_path'] = $path;
        }

        unset($validated['resume']);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->fresh()
        ]);
    }
}
