<?php

namespace App\Http\Controllers;
use App\Models\User;

use App\Models\Job;
use App\Notifications\JobApprovedNotification;
use App\Notifications\JobRejectedNotification;

class AdminJobController extends Controller
{
    public function approve(Job $job)
    {
       
        if ($job->status === 'approved') {
            return response()->json([
                'message' => 'Job already approved'
            ], 400);
        }

        $job->update(['status' => 'approved']);

        if ($job->user) {
            $job->user->notify(new JobApprovedNotification($job));
        }

        return response()->json([
            'message' => 'Job approved successfully',
            'job' => $job
        ]);
    }

    public function reject(Job $job)
    {
        if ($job->status === 'rejected') {
            return response()->json([
                'message' => 'Job already rejected'
            ], 400);
        }

        $job->update(['status' => 'rejected']);

        if ($job->user) {
            $job->user->notify(new JobRejectedNotification($job));
        }

        return response()->json([
            'message' => 'Job rejected successfully',
            'job' => $job
        ]);
    }

    
    public function dashboard()
    {
        return response()->json([
            'total_users' => User::count(),
            'total_jobs' => Job::count(),
            'pending_jobs' => Job::where('status', 'pending')->count(),
            'approved_jobs' => Job::where('status', 'approved')->count(),
            'rejected_jobs' => Job::where('status', 'rejected')->count(),
        ]);
    }

    public function pending()
    {
        $jobs = Job::with('user', 'category')->where('status', 'pending')->latest()->paginate(10);
        return response()->json($jobs);
    }

    public function pendingCompanies()
    {
        $companies = User::role('employer')->where('status', 'pending')->latest()->paginate(10);
        return response()->json($companies);
    }

    public function approveCompany(User $user)
    {
        if (!$user->hasRole('employer')) {
            abort(403, 'User is not an employer.');
        }

        $user->update(['status' => 'approved']);

        return response()->json([
            'message' => 'Company approved successfully',
            'user' => $user
        ]);
    }
}