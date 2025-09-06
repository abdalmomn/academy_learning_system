<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Exam;
use App\Models\LeaderBoard;
use App\Models\McqAnswer;
use App\Models\McqOption;
use App\Models\ProjectSubmission;
use App\Models\Question;
use App\Models\Strike;
use App\Models\User;
use App\Models\Wallet;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExamService
{

    public function create_exam($exam_dto): array
    {
        $teacher = Auth::user();
        if (!$teacher->hasRole('teacher')){
            return [
                'data' => null,
                'message' => 'must be teacher to create exam'
            ];
        }
        try {
            $exam = Exam::query()->create((array)$exam_dto);
            if ($exam_dto->exam_mode === 'project') {
                $exam->duration_minutes = null;
            } else {
                $exam->duration_minutes = $exam_dto->start_date->diffInMinutes($exam_dto->end_date);
            }
            $exam->save();

            if ($exam_dto->video_id == null){
                unset($exam['video_id']);
            }
            unset($exam['updated_at']); unset($exam['created_at']);
            Log::info('exam created successfully', [
                'exam id' => $exam->id,
                'course id' => $exam_dto->course_id,
                'teacher id' => Auth::id()
            ]);
            return [
                'data' => $exam,
                'message' => 'exam created successfully'
            ];
        }catch (Exception $e){
            Log::warning('there is error with creating exam', [
                'message' => $e->getMessage(),
                'course_id' => $exam_dto->course_id,
                'teacher id' => Auth::id()
            ]);

            return [
                'data' => null,
                'message' => 'there is problem in creating exam'
            ];
        }
    }

    public function show_single_exam($exam_id): array
    {
        try {
            $exam = Exam::query()
                ->with([
                    'questions' => function($q) {
                        $q->select('id','question_text','question_type','mark','exam_id')
                            ->with('options:id,question_id,option_text,is_correct');
                    }
                ])
                ->find($exam_id);
            $course = Course::query()->find($exam->course_id);
            $exam['course_name'] = $course->course_name;
            unset($exam['course_id']);
            if (!$exam) {
                return [
                    'data' => null,
                    'message' => 'The exam is not found',
                    'code' => 404
                ];
            }

            if ($exam->video_id == null) {
                unset($exam['video_id']);
            }

            unset($exam['created_at'], $exam['updated_at']);

            return [
                'data' => $exam,
                'message' => 'Exam retrieved successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Fetching exam failed', ['error' => $e->getMessage()]);
            return [
                'data' => null,
                'message' => 'Failed to retrieve the exam',
                'code' => 500
            ];
        }
    }
    public function show_exams_by_course($course_id): array
    {
        try {
            $exams = Exam::query()
                ->where('course_id', $course_id)
                ->with([
                    'questions' => function($q) {
                        $q->select('id','question_text','question_type','mark','exam_id')
                            ->with('options:id,question_id,option_text,is_correct');
                    }
                ])
                ->get(['id', 'title', 'description', 'exam_mode', 'course_id', 'video_id', 'duration_minutes']);

            if ($exams->isEmpty()) {
                return [
                    'data' => null,
                    'message' => 'No exams found for this course',
                    'code' => 404
                ];
            }

            $course = Course::query()->find($course_id);
            foreach ($exams as $exam) {
                $exam['course_name'] = $course->course_name;
                unset($exam['course_id']);
            }

            return [
                'data' => $exams,
                'message' => 'Exams retrieved successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Fetching exams failed', ['error' => $e->getMessage()]);
            return [
                'data' => null,
                'message' => 'Failed to retrieve exams',
                'code' => 500
            ];
        }
    }


    public function show_all_exams(): array
    {
        $user = Auth::user();
        if (!$user->hasRole('admin')){
            return [
                'data' => null,
                'message' => 'must be admin to show all exams'
            ];
        }
        $exams = Exam::all();
        foreach ($exams as $exam){
            $course = Course::query()->find($exam->course_id);
            $exam['course_name'] = $course->course_name;
            unset($exam['course_id']);
        }
        if ($exams->isEmpty()){
            return [
                'data' => null,
                'message' => 'there is no exams right now'
            ];
        }
        foreach ($exams as $exam){
            if ($exam->video_id == null){
                unset($exam['video_id']);
            }
            unset($exam['updated_at']); unset($exam['created_at']);
        }
        return [
            'data' => $exams,
            'message' => 'all exams retrieved'
        ];
    }

    public function update_exam($exam_dto,$exam_id): array
    {
        try {
        $user = Auth::user();
        if (!$user->hasRole('teacher')){
            return [
                'data' => null,
                'message' => 'must be teacher to update the exam',
                'code' => 401
            ];
        }
        $exam = Exam::query()->find($exam_id);
        if (!$exam){
            return [
                'data' => null,
                'message' => 'exam not found',
                'code' => 404
            ];
        }
        $exam->update((array)$exam_dto);
        if ($exam_dto->exam_mode === 'project') {
            $exam->duration_minutes = null;
        } else {
            $exam->duration_minutes = $exam_dto->start_date->diffInMinutes($exam_dto->end_date);
        }
        $exam->save();
        if ($exam->video_id == null){
            unset($exam['video_id']);
        }
        unset($exam['updated_at']); unset($exam['created_at']);
        Log::info('exam updated', [
            'exam id' => $exam->id,
            'teacher id' => Auth::id()
        ]);
        return [
            'data' => $exam,
            'message' => 'exam updated successfully',
            'code' => 200
        ];
        }catch(Exception $e){
            Log::warning('error in update exam', [
                'message' => $e->getMessage(),
                'teacher id' => Auth::id()
            ]);
            return [
                'data' => null,
                'message' => 'there is error in update exam',
                'code' => 402
            ];
        }
    }

    public function delete_exam($exam_id): array
    {
        $user = Auth::user();
        if (!$user->hasRole('teacher')){
            return [
                'data' => null,
                'message' => 'must be teacher to delete the exam'
            ];
        }
        $exam = Exam::query()->find($exam_id);
        if (!$exam){
            return [
                'data' => null,
                'message' => 'exam not found',
                'code' => 404
            ];
        }
        Exam::query()
            ->where('id' , $exam_id)
            ->with([
                'questions' => function($q){
                    $q->with('options');
                }
            ])->delete();
        return [
            'data' => null,
            'message' => 'exam deleted successfully'
        ];
    }

    public function store_exam_answer($request): array
    {
        $user = Auth::user();
        if (!$user->hasRole(['woman', 'child'])) {
            return [
                'data' => null,
                'message' => 'only for students'
            ];
        }
        $user_id = Auth::id();
        $question_id = $request['question_id'];
        $option_id   = $request['selected_option_id'];
        $request['user_id'] = $user_id;

        $already_answered = McqAnswer::where('user_id', $user_id)
            ->where('question_id', $question_id)
            ->exists();

        if ($already_answered) {
            return [
                'data' => null,
                'message' => 'You have already answered this question.'
            ];
        }

        $option = McqOption::where('id', $option_id)
            ->where('question_id', $question_id)
            ->first();

        if (!$option) {
            return [
                'data' => null,
                'message' => 'Invalid option for this question.'
            ];
        }

        $request['is_correct'] = $option->is_correct;

        $answer = McqAnswer::create($request);

        unset($answer['created_at']);unset($answer['updated_at']);
        return [
            'data' => $answer,
            'message' => 'Answer stored successfully.'
        ];
    }

    public function get_exam_result($exam_id): array
    {
        $user = Auth::user();
        $user_id = Auth::id();

        if (!$user->hasRole(['woman', 'child'])) {
            return [
                'data' => null,
                'message' => 'only for students'
            ];
        }

        $exam = Exam::with('course')->find($exam_id);
        if (!$exam) {
            return [
                'data' => null,
                'message' => 'Exam not found'
            ];
        }

        $course = $exam->course;
        if (!$course) {
            return [
                'data' => null,
                'message' => 'This exam is not linked to any course.'
            ];
        }

        $questions = Question::where('exam_id', $exam_id)->pluck('id');
        $totalQuestions = $questions->count();


        if ($totalQuestions == 0) {
            return [
                'data' => null,
                'message' => 'This exam has no questions.'
            ];
        }

        $correctAnswers = McqAnswer::where('user_id', $user_id)
            ->whereIn('question_id', $questions)
            ->where('is_correct', true)
            ->count();

        $percentage = round(($correctAnswers / $totalQuestions) * 100, 2);
        $strike =Strike::updateOrCreate(
            [
                'user_id' => $user->id,
                'date' => now()->toDateString()
            ],
            [
                'streak' => DB::raw('streak + 1'),
                'attended' => true
            ]
        );
//            $strike->streak += 1;
        $strike->attended =true ;
        $strike->save();
        $leader = LeaderBoard::query()
            ->where('leader_id' , Auth::id())
            ->first();
        $leader->points += $percentage/10;
        $leader->update(['points' => $leader->points]);
        $wallet = Wallet::query()
            ->where('user_id',Auth::id())
            ->first();
        $wallet->balance += $percentage/10;
        $wallet->update(['balance' , $wallet->balance]);
        if ($percentage >= 50) {
//            $certificate = $this->generateCertificate($user, $course);

            return [
                'data' => [
                    'total_questions' => $totalQuestions,
                    'correct_answers' => $correctAnswers,
                    'percentage' => $percentage,
//                    'certificate_url' => Storage::url($certificate->certificate_url),
                ],
                'message' => 'Congratulations! You passed the exam and earned a ' . ($percentage / 10) . ' points',
            ];
        }

        return [
            'data' => [
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctAnswers,
                'percentage' => $percentage,
            ],
            'message' => 'Sorry! you have failed in the exam'
        ];
    }


    public function submit_project_by_students($request):array
    {
        try {
            $user = Auth::user();

            if (!$user->hasRole(['woman', 'child'])) {
                return [
                    'data' => null,
                    'message' => 'Only students can submit projects'
                ];
            }
            $question_id = $request['question_id'];
            $user_id = Auth::id();

            $existing = ProjectSubmission::where('question_id', $question_id)
                ->where('user_id', $user_id)
                ->first();

            if ($existing) {
                return [
                    'data' => null,
                    'message' => 'You have already submitted a file for this project'
                ];
            }
            $request['user_id'] = $user_id;
            $submission = ProjectSubmission::query()->create($request);
            $strike =Strike::updateOrCreate(
                [
                    'user_id' => $user_id,
                    'date' => now()->toDateString()
                ],
                [
                    'streak' => DB::raw('streak + 1'),
                    'attended' => true
                ]
            );
            $strike->streak += 1;
            $strike->attended =true ;
            $strike->save();
            unset($submission['created_at']);unset($submission['updated_at']);
            return [
                'data' => $submission,
                'message' => 'file submitted successfully'
            ];
        } catch (\Exception $e) {
            Log::warning('Project submission failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return [
                'data' => null,
                'message' => $e->getMessage()
//                'message' => 'Failed to submit the project'
            ];
        }
    }

    public function show_project_result($exam_id): array
    {
        try {
            $user = Auth::user();

            if (!$user->hasRole('teacher')) {
                return [
                    'data' => null,
                    'message' => 'must be teacher to show projects'
                ];
            }

            $query = Exam::query()
                ->whereHas('course', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->with(['questions.projectSubmissions.user:id,username,email']);

            if ($exam_id) {
                $query->where('id', $exam_id);
            }

            $exams = $query->get(['id', 'title', 'course_id']);

            if ($exam_id && !Exam::where('id', $exam_id)->exists()) {
                return [
                    'data' => null,
                    'message' => 'Exam not found'
                ];
            }

            if ($exam_id && $exams->isEmpty()) {
                return [
                    'data' => null,
                    'message' => 'This exam does not belong to you'
                ];
            }

            if ($exams->isEmpty()) {
                return [
                    'data' => [],
                    'message' => 'No exams found for this teacher'
                ];
            }
            // نرتب البيانات
            $result = $exams->map(function ($exam) {
                return [
                    'exam_id' => $exam->id,
                    'exam_title' => $exam->title,
                    'projects' => $exam->questions->flatMap(function ($question) {
                        return $question->projectSubmissions->map(function ($submission) use ($question) {
                            return [
                                'submission_id' => $submission->id,
                                'file_path' => $submission->file_path,
                                'grade' => $submission->grade,
                                'feedback' => $submission->feedback,
                                'student' => $submission->user,
                                'question_id' => $question->id,
                                'question_text' => $question->question_text,
                            ];
                        });
                    })->values()
                ];
            });

            return [
                'data' => $result,
                'message' => 'Project submissions retrieved successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Failed to retrieve project submissions', [
                'error' => $e->getMessage()
            ]);

            return [
                'data' => null,
                'message' => 'Failed to retrieve project submissions'
            ];
        }
    }


    public function show_my_project_result($project_id): array
    {
        try {
            $user = Auth::user();

            if (!$user->hasRole(['woman', 'child'])) {
                return [
                    'data' => null,
                    'message' => 'Only students can view project results'
                ];
            }

            $submission = ProjectSubmission::with('question.exam')
                ->where('id', $project_id)
                ->where('user_id', $user->id) // ✅ يمنع الوصول لمشاريع غيرو
                ->first();

            if (!$submission) {
                return [
                    'data' => null,
                    'message' => 'Project not found for this user'
                ];
            }

            // ✅ إذا لسا ما تصححت
            if (is_null($submission->grade)) {
                return [
                    'data' => [
                        'exam_title' => $submission->question->exam->title ?? null,
                        'project_id' => $submission->id,
                        'grade' => null,
                        'feedback' => null,
                    ],
                    'message' => 'Your project is under review'
                ];
            }

            // ✅ رجع النتيجة
            return [
                'data' => [
                    'exam_title' => $submission->question->exam->title ?? null,
                    'project_id' => $submission->id,
                    'grade' => $submission->grade,
                    'feedback' => $submission->feedback,
                ],
                'message' => 'Project result retrieved successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Failed to show project result', [
                'error' => $e->getMessage(),
                'project_id' => $project_id,
                'user_id' => Auth::id(),
            ]);

            return [
                'data' => null,
                'message' => 'Failed to retrieve project result'
            ];
        }
    }

    public function correct_project($submission_id, array $data): array
    {
        try {
            $user = Auth::user();

            if (!$user->hasRole('teacher')) {
                return [
                    'data' => null,
                    'message' => 'Only teachers can grade projects'
                ];
            }

            // ✅ نجيب الـ submission مع كامل السلسلة لنوصل للـ exam
            $submission = ProjectSubmission::with('question.exam.course')
                ->find($submission_id);

            if (!$submission) {
                return [
                    'data' => null,
                    'message' => 'Project submission not found',
                    'code' => 404
                ];
            }
            $exam = $submission->question->exam ?? null;

            if (!$exam) {
                return [
                    'data' => null,
                    'message' => 'Exam not found for this project submission'
                ];
            }

            $course_owner_id = $submission->question->exam->course->user_id ?? null;

            if ($course_owner_id !== Auth::id()) {
                return [
                    'data' => null,
                    'message' => 'You are not authorized to grade this project',
                    'code' => 403
                ];
            }

            if ($submission->user_id === Auth::id()) {
                return [
                    'data' => null,
                    'message' => 'You cannot grade your own project'
                ];
            }

            if (!is_null($submission->grade)) {
                return [
                    'data' => null,
                    'message' => 'This project has already been graded'
                ];
            }

            $submission->update([
                'grade' => $data['grade'],
                'feedback' => $data['feedback'] ?? null,
            ]);
            $leader = LeaderBoard::query()
                ->where('leader_id' , $submission->user_id)
                ->first();
            $leader->points += $submission->grade/10;
            $leader->update(['points' => $leader->points]);
            $wallet = Wallet::query()
                ->where('user_id',$submission->user_id)
                ->first();
            $wallet->balance += $submission->grade/10;
            $wallet->update(['balance' , $wallet->balance]);

            return [
                'data' => null,
                'message' => 'Project graded successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Failed to grade project', [
                'error' => $e->getMessage(),
                'submission_id' => $submission_id
            ]);

            return [
                'data' => null,
                'message' => 'Failed to grade the project'
            ];
        }
    }


}
