<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // Drop existing FK using conventional name
            try {
                $table->dropForeign('comments_job_id_foreign');
            } catch (\Throwable $e) {
                // FK may not exist or has different name, continue
            }
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('job_id')
                  ->references('id')
                  ->on('job_listings')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            try {
                $table->dropForeign(['job_id']);
            } catch (\Throwable $e) {
                // ignore
            }

            $table->foreign('job_id')
                  ->references('id')
                  ->on('jobs')
                  ->cascadeOnDelete();
        });
    }
};
