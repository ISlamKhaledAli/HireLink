<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    public function recordView(Request $request, Job $job): JsonResponse
    {
        $ip = $request->ip();

        $viewedToday = $job->views()
            ->where('ip_address', $ip)
            ->whereDate('viewed_at', now()->toDateString())
            ->exists();

        if (!$viewedToday) {
            $job->views()->create([
                'user_id' => auth('sanctum')->id(), // Will be null for guests
                'ip_address' => $ip,
                'viewed_at' => now(),
            ]);
        }

        return response()->json(['message' => 'View recorded successfully']);
    }

    public function analytics(Request $request, Job $job): JsonResponse
    {
        if ($job->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $totalApplications = $job->applications()->count();
        $acceptedApplications = $job->applications()->where('status', 'accepted')->count();

        $acceptanceRate = $totalApplications > 0
            ? round(($acceptedApplications / $totalApplications) * 100, 2)
            : 0;

        $appliedToday = $job->applications()
            ->whereDate('created_at', now()->toDateString())
            ->count();

        return response()->json([
            'views_count' => $job->views()->count(),
            'applications_count' => $totalApplications,
            'acceptance_rate' => $acceptanceRate,
            'applied_today' => $appliedToday,
        ]);
    }
}
