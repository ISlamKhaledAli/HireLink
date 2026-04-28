<?php

namespace App\Http\Controllers;

use App\Models\Job;

class AdminJobController extends Controller
{
    public function approve(Job $job)
    {
        $job->update(['status' => 'approved']);

        return response()->json(['message' => 'Job approved successfully.']);
    }

    public function reject(Job $job)
    {
        $job->update(['status' => 'rejected']);

        return response()->json(['message' => 'Job rejected successfully.']);
    }
}
