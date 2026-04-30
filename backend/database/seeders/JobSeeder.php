<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $categories = Category::all();

        $jobs = [
            [
                'title' => 'Senior Frontend Engineer',
                'description' => 'We are looking for a senior frontend engineer with Vue.js expertise.',
                'location' => 'New York, NY',
                'work_type' => 'remote',
                'salary_min' => 120000,
                'salary_max' => 180000,
                'deadline' => now()->addDays(30),
                'status' => 'approved',
            ],
            [
                'title' => 'Product Designer',
                'description' => 'Join our design team to create beautiful SaaS products.',
                'location' => 'San Francisco, CA',
                'work_type' => 'hybrid',
                'salary_min' => 90000,
                'salary_max' => 140000,
                'deadline' => now()->addDays(15),
                'status' => 'approved',
            ],
            [
                'title' => 'Backend Developer (Laravel)',
                'description' => 'Help us build scalable APIs using Laravel 11.',
                'location' => 'Remote',
                'work_type' => 'remote',
                'salary_min' => 100000,
                'salary_max' => 160000,
                'deadline' => now()->addDays(20),
                'status' => 'approved',
            ],
            [
                'title' => 'Marketing Manager',
                'description' => 'Lead our marketing efforts and grow our brand.',
                'location' => 'Austin, TX',
                'work_type' => 'onsite',
                'salary_min' => 80000,
                'salary_max' => 120000,
                'deadline' => now()->addDays(45),
                'status' => 'approved',
            ],
        ];

        foreach ($jobs as $jobData) {
            Job::create(array_merge($jobData, [
                'user_id' => $admin->id,
                'category_id' => $categories->random()->id,
            ]));
        }
    }
}
