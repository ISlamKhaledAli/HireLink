<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /**
     * Apply for a job.
     */
    public function apply(Request $request)
    {
        // Auth: candidate only
        if (!auth()->user()->hasRole('candidate')) {
            abort(403, 'Only candidates can apply for jobs.');
        }

        $request->validate([
            'job_id' => 'required|exists:job_listings,id',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'resume' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        // Check: candidate hasn't already applied to this job
        $duplicate = Application::where('job_id', $request->job_id)
            ->where('candidate_id', auth()->id())
            ->exists();

        if ($duplicate) {
            return response()->json(['message' => 'You have already applied for this job.'], 422);
        }

        $resumePath = null;
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'local');
        }

        $application = Application::create([
            'job_id' => $request->job_id,
            'candidate_id' => auth()->id(),
            'email' => $request->email,
            'phone' => $request->phone,
            'resume_path' => $resumePath,
            'status' => 'pending',
        ]);

        return response()->json($application, 201);
    }

    /**
     * Cancel an application.
     */
    public function cancel(Application $application)
    {
        // Auth: only the candidate who owns this application
        if ($application->candidate_id != auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        // Check status is still 'pending'
        if ($application->status !== 'pending') {
            return response()->json(['message' => 'Only pending applications can be cancelled.'], 422);
        }

        $application->delete();

        return response()->json(['message' => 'Application deleted successfully.'], 200);
    }

    /**
     * Update application status.
     */
    public function updateStatus(Request $request, Application $application)
    {
        // Auth: only the employer who owns the job
        if ($application->job->user_id != auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $application->update([
            'status' => $request->status,
        ]);

        return response()->json($application, 200);
    }

    /**
     * List applications for an employer.
     */
    public function index(Request $request)
    {
        // Auth: employer only
        if (!auth()->user()->hasRole('employer')) {
            abort(403, 'Only employers can view applications.');
        }

        // Return all applications for jobs that belong to this employer
        $applications = Application::with(['job', 'candidate'])
            ->whereHas('job', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->get();

        return response()->json($applications);
    }
}
