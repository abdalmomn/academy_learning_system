<?php

namespace App\Services;
use App\Models\Category;
use App\Models\Course;
use App\Models\Interest;
use App\DTO\InterestDTO;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
class InterestService
{
    public function getAll($userId)
    {
        try {
            $interests = Interest::with('category')
                ->where('user_id', $userId)
                ->latest()
                ->get();

            if ($interests->isEmpty()) {
                return [
                    'data' => null,
                    'message' => 'No interests found'
                ];
            }

            return [
                'data' => $interests,
                'message' => 'All interests'
            ];
        } catch (Exception $e) {
            Log::error('Fetching interests failed', ['error' => $e->getMessage()]);
            return [
                'data' => null,
                'message' => 'Failed to fetch interests'
            ];
        }
    }


    public function show_courses_as_interests(): array
    {
        $user = auth()->user();
        $user_interests = $user->interests;
        $category_ids = [];
        foreach ($user_interests as $interest){
            $category_ids[] = $interest->category_id;
        }
        $courses = Course::query()
            ->whereIn('category_id',$category_ids)
            ->get(['id', 'course_name','description' , 'poster', 'price', 'rating','is_paid', 'status','user_id','category_id']);
        foreach ($courses as $course){
            $course['teacher_name'] = User::query()->select(['username as teacher_name'])->where('id', $course->user_id)->first();
            unset($course['user_id']);
            foreach ($category_ids as $category_id){
                $course['category_name'] = Category::query()->where('id',$category_id)->pluck('category_name');
                unset($course['category_id']);
            }
        }
        return [
            'data' => $courses,
            'message' => 'return courses based on user interests'
        ];
    }
    public function add(InterestDTO $dto)
    {
        DB::beginTransaction();
        try {
            $exists = Interest::where('user_id', $dto->user_id)
                ->where('category_id', $dto->category_id)
                ->first();

            if ($exists) {
                return [
                    'data' => null,
                    'message' => 'Interest already exists'
                ];
            }

            $interest = Interest::create([
                'user_id' => $dto->user_id,
                'category_id' => $dto->category_id,
            ]);

            DB::commit();
            Log::info('Interest added', [
                'user_id' => $dto->user_id,
                'category_id' => $dto->category_id
            ]);

            return [
                'data' => $interest,
                'message' => 'Interest added successfully'
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Adding interest failed', [
                'error' => $e->getMessage(),
                'user_id' => $dto->user_id,
                'category_id' => $dto->category_id
            ]);
            return [
                'data' => null,
                'message' => 'Failed to add interest'
            ];
        }
    }

    public function update($id, InterestDTO $dto)
    {
        DB::beginTransaction();
        try {
            $interest = Interest::find($id);
            if (!$interest) {
                return [
                    'data' => null,
                    'message' => 'Interest not found'
                ];
            }

            $interest->update([
                'category_id' => $dto->category_id
            ]);

            DB::commit();
            Log::info('Interest updated', ['id' => $id]);

            return [
                'data' => $interest,
                'message' => 'Interest updated successfully'
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Updating interest failed', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            return [
                'data' => null,
                'message' => 'Failed to update interest'
            ];
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $interest = Interest::find($id);
            if (!$interest) {
                return [
                    'data' => null,
                    'message' => 'Interest not found'
                ];
            }

            $interest->delete();
            DB::commit();
            Log::info('Interest deleted', ['id' => $id]);

            return [
                'data' => null,
                'message' => 'Interest deleted successfully'
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Deleting interest failed', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            return [
                'data' => null,
                'message' => 'Failed to delete interest'
            ];
        }
    }
    public function toggle(InterestDTO $dto)
    {
        try {
            $interest = Interest::where('user_id', $dto->user_id)
                ->where('category_id', $dto->category_id)
                ->first();

            if ($interest) {
                $interest->delete();
                return [
                    'data' => null,
                    'message' => 'Interest removed successfully'
                ];
            }

            $new = Interest::create([
                'user_id' => $dto->user_id,
                'category_id' => $dto->category_id,
            ]);

            return [
                'data' => $new,
                'message' => 'Interest added successfully'
            ];
        } catch (Exception $e) {
            Log::error('Toggling interest failed', [
                'error' => $e->getMessage(),
                'user_id' => $dto->user_id,
                'category_id' => $dto->category_id
            ]);
            return [
                'data' => null,
                'message' => 'Failed to toggle interest'
            ];
        }
    }
}
