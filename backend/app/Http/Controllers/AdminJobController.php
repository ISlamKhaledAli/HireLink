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
        'users_count' => User::count(),
        'jobs_count' => Job::count(),
        'pending_jobs' => Job::where('status', 'pending')->count(),
        'approved_jobs' => Job::where('status', 'approved')->count(),
        'rejected_jobs' => Job::where('status', 'rejected')->count(),
    ]);
}


}