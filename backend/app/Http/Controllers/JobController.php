<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::with(['category', 'user'])->where('status', 'approved');

        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->keyword.'%')
                    ->orWhere('description', 'like', '%'.$request->keyword.'%');
            });
        }

        // Support both 'category' and 'category_id' for robustness
        if ($request->filled('category_id') || $request->filled('category')) {
            $query->where('category_id', $request->category_id ?? $request->category);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%'.$request->location.'%');
        }

        // Handle multiple work types (comma-separated string from frontend)
        if ($request->filled('work_type')) {
            $types = explode(',', $request->work_type);
            $query->whereIn('work_type', $types);
        }

        // Salary filtering
        if ($request->filled('salary_min')) {
            $query->where('salary_min', '>=', $request->salary_min);
        }
        if ($request->filled('salary_max')) {
            $query->where('salary_max', '<=', $request->salary_max);
        }

        // Date posted filtering
        if ($request->filled('date_posted')) {
            $date = match($request->date_posted) {
                'today' => now()->startOfDay(),
                'week' => now()->subDays(7),
                'month' => now()->subDays(30),
                default => null
            };
            if ($date) {
                $query->where('created_at', '>=', $date);
            }
        }

        return JobResource::collection($query->latest()->paginate(15));
    }

    public function store(StoreJobRequest $request)
    {
        $job = $request->user()->jobs()->create($request->validated());

        return new JobResource($job->load(['category', 'user']));
    }

    public function show(Request $request, Job $job)
    {
        if ($job->status !== 'approved' && $request->user()?->id !== $job->user_id) {
            abort(403, 'Unauthorized access to this job listing.');
        }

        return new JobResource($job->load(['category', 'user']));
    }

    public function update(StoreJobRequest $request, Job $job)
    {
        Gate::authorize('update', $job);

        $job->update(array_merge($request->validated(), ['status' => 'pending']));

        return new JobResource($job->fresh()->load(['category', 'user']));
    }

    public function destroy(Job $job)
    {
        Gate::authorize('delete', $job);

        $job->delete();

        return response()->noContent();
    }
}
