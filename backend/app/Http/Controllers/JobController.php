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

        return JobResource::collection($query->paginate(15));
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
