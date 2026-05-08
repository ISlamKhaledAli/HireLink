<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        // Create an employer user if none exists
        $employer = User::whereHas('roles', fn ($q) => $q->where('name', 'employer'))->first();

        if (!$employer) {
            $employer = User::create([
                'name' => 'HireLink Demo Employer',
                'email' => 'employer@hirelink.test',
                'password' => Hash::make('password123'),
            ]);
            $employer->assignRole('employer');
        }

        // Create a candidate user if none exists
        $candidate = User::whereHas('roles', fn ($q) => $q->where('name', 'candidate'))->first();

        if (!$candidate) {
            $candidate = User::create([
                'name' => 'Demo Candidate',
                'email' => 'candidate@hirelink.test',
                'password' => Hash::make('password123'),
            ]);
            $candidate->assignRole('candidate');
        }

        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Run CategorySeeder first.');
            return;
        }

        $jobs = [
            [
                'title' => 'Senior Full-Stack Engineer',
                'description' => "We are looking for a Senior Full-Stack Engineer to join our growing team.\n\n**Responsibilities:**\n- Design and implement scalable web applications\n- Mentor junior developers and conduct code reviews\n- Collaborate with product and design teams\n- Optimize application performance\n\n**Requirements:**\n- 5+ years of experience with modern web frameworks\n- Proficiency in React/Vue.js and Node.js or Laravel\n- Strong understanding of databases and API design\n- Excellent communication skills",
                'location' => 'San Francisco, CA',
                'work_type' => 'hybrid',
                'salary_range' => '$120k – $180k',
                'category' => 'Engineering',
            ],
            [
                'title' => 'UI/UX Designer',
                'description' => "Join our design team to craft beautiful, user-centered experiences.\n\n**Responsibilities:**\n- Create wireframes, prototypes, and high-fidelity mockups\n- Conduct user research and usability testing\n- Maintain and evolve our design system\n- Work closely with engineering to ensure pixel-perfect implementation\n\n**Requirements:**\n- 3+ years of product design experience\n- Proficiency in Figma and design tools\n- Strong portfolio demonstrating user-centered design\n- Understanding of accessibility standards",
                'location' => 'New York, NY',
                'work_type' => 'remote',
                'salary_range' => '$90k – $130k',
                'category' => 'Design',
            ],
            [
                'title' => 'Digital Marketing Manager',
                'description' => "Lead our digital marketing strategy and grow our brand presence.\n\n**Responsibilities:**\n- Develop and execute multi-channel marketing campaigns\n- Manage social media, SEO, and paid advertising\n- Analyze campaign performance and optimize ROI\n- Build and maintain brand partnerships\n\n**Requirements:**\n- 4+ years in digital marketing\n- Experience with Google Ads, Meta Ads, and analytics platforms\n- Strong analytical and creative skills\n- Proven track record of driving growth",
                'location' => 'Austin, TX',
                'work_type' => 'remote',
                'salary_range' => '$85k – $120k',
                'category' => 'Marketing',
            ],
            [
                'title' => 'Enterprise Account Executive',
                'description' => "Drive revenue growth by closing enterprise deals.\n\n**Responsibilities:**\n- Manage full sales cycle from prospecting to close\n- Build relationships with C-level executives\n- Achieve quarterly and annual revenue targets\n- Collaborate with solutions engineering team\n\n**Requirements:**\n- 5+ years in B2B enterprise sales\n- Proven track record of exceeding quotas\n- Experience with CRM tools (Salesforce preferred)\n- Excellent presentation and negotiation skills",
                'location' => 'Chicago, IL',
                'work_type' => 'onsite',
                'salary_range' => '$100k – $160k + Commission',
                'category' => 'Sales',
            ],
            [
                'title' => 'Financial Analyst',
                'description' => "Support strategic decision-making with financial insights.\n\n**Responsibilities:**\n- Build financial models and forecasts\n- Prepare monthly and quarterly reports\n- Analyze business performance and market trends\n- Support budgeting and planning processes\n\n**Requirements:**\n- Bachelor's in Finance, Accounting, or related field\n- 2+ years of financial analysis experience\n- Advanced Excel and financial modeling skills\n- Experience with BI tools (Tableau, Power BI)",
                'location' => 'Boston, MA',
                'work_type' => 'hybrid',
                'salary_range' => '$75k – $100k',
                'category' => 'Finance',
            ],
            [
                'title' => 'Product Manager',
                'description' => "Shape the future of our product and drive user value.\n\n**Responsibilities:**\n- Define product vision, strategy, and roadmap\n- Prioritize features based on user feedback and data\n- Work with engineering, design, and marketing teams\n- Track KPIs and iterate on product direction\n\n**Requirements:**\n- 3+ years of product management experience\n- Strong analytical and communication skills\n- Experience with agile methodologies\n- Technical background preferred",
                'location' => 'Seattle, WA',
                'work_type' => 'remote',
                'salary_range' => '$110k – $150k',
                'category' => 'Product Management',
            ],
        ];

        foreach ($jobs as $jobData) {
            $category = $categories->firstWhere('name', $jobData['category']);
            if (!$category) {
                continue;
            }

            Job::firstOrCreate(
                ['title' => $jobData['title'], 'user_id' => $employer->id],
                [
                    'category_id' => $category->id,
                    'description' => $jobData['description'],
                    'location' => $jobData['location'],
                    'work_type' => $jobData['work_type'],
                    'salary_range' => $jobData['salary_range'],
                    'deadline' => now()->addMonths(2)->format('Y-m-d'),
                    'status' => 'approved',
                ]
            );
        }
    }
}
