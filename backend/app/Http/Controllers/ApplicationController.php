<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use App\Notifications\ApplicationStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
        ]);

        return response()->json($application->load(['job', 'candidate']), 201);
    }

    /**
     * Cancel an application.
     */
    public function cancel(Application $application)
    {
        Gate::authorize('cancel', $application);

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
        Gate::authorize('updateStatus', $application);

        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $application->update([
            'status' => $request->status,
        ]);

        $application->candidate->notify(new ApplicationStatusUpdated($application));

        return response()->json($application, 200);
    }

    /**
     * List applications — works for both employers and candidates.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Employer: return applications for their jobs
        if ($user->hasRole('employer')) {
            $applications = Application::with(['job', 'candidate'])
                ->whereHas('job', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->latest()
                ->get();

            return response()->json($applications);
        }

        // Candidate: return their own applications
        if ($user->hasRole('candidate')) {
            $applications = Application::with(['job.user', 'job.category'])
                ->where('candidate_id', $user->id)
                ->latest()
                ->get();

            return response()->json($applications);
        }

        // Admin: return all
        if ($user->hasRole('admin')) {
            $applications = Application::with(['job', 'candidate'])
                ->latest()
                ->get();

            return response()->json($applications);
        }

        abort(403, 'Unauthorized.');
    }
    /**
     * Download or view the candidate's resume.
     */
    public function downloadResume(Application $application)
    {
        // Only the employer who owns the job, the candidate themselves, or an admin can view the CV.
        $user = auth()->user();
        if ($user->hasRole('admin') || $user->id === $application->candidate_id || $user->id === $application->job->user_id) {
            if (!$application->resume_path || !\Storage::disk('local')->exists($application->resume_path)) {
                abort(404, 'Resume file not found.');
            }

            return \Storage::disk('local')->download($application->resume_path);
        }

        abort(403, 'Unauthorized.');
    }
}
