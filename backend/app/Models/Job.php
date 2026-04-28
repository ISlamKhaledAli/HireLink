<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Job extends Model
{
    /**
     * Queue listings use this table name because Laravel reserves `jobs` for queue payloads.
     *
     * @var string
     */
    protected $table = 'job_listings';

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'location',
        'work_type',
        'salary_range',
        'deadline',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
