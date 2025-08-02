<?php

namespace App\Jobs;

use App\Models\Course;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ApproveScheduledCourseJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    protected $course_id;

    public function __construct($course_id)
    {
        $this->course_id = $course_id;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $course = Course::find($this->course_id);
        if ($course && $course->status !== 'approved') {
            $course->update(['status' => 'published']);
            Log::info('scheduled course approved automatically', ['id' => $this->course_id]);
        }
    }
}
