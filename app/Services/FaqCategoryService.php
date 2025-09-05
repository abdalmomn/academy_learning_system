<?php

namespace App\Services;
use App\DTO\FaqCategoryDTO;
use App\Models\Faq_categories;
use App\Models\FaqCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;


class FaqCategoryService
{
    public function getAll()
    {
        try {
            $categories = Faq_categories::latest()->get();
            return [
                'data' => $categories,
                'message' => $categories->isEmpty() ? 'No FAQ categories found' : 'All FAQ categories'
            ];
        } catch (Exception $e) {
            Log::error('Fetching FAQ categories failed', ['error' => $e->getMessage()]);
            return ['data' => null, 'message' => 'Failed to fetch FAQ categories'];
        }
    }

    public function getById($id)
    {
        try {
            $category = Faq_categories::with('faq')->find($id);
            return [
                'data' => $category,
                'message' => $category ? 'FAQ Category details' : 'FAQ Category not found'
            ];
        } catch (Exception $e) {
            Log::error('Fetching FAQ category failed', ['error' => $e->getMessage()]);
            return ['data' => null, 'message' => 'Failed to fetch FAQ category'];
        }
    }

    public function store(FaqCategoryDTO $dto)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole( ['admin', 'supervisor'])) {
            return ['data' => null, 'message' => 'Unauthorized - admin or supervisor only'];
        }

        DB::beginTransaction();
        try {
            $category = Faq_categories::create(['faq_category_name' => $dto->faq_category_name]);
            DB::commit();
            Log::info('FAQ category created', ['id' => $category->id]);

            return ['data' => $category, 'message' => 'FAQ category created successfully'];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('FAQ category creation failed', ['error' => $e->getMessage()]);
            return ['data' => null, 'message' => 'Failed to create FAQ category'];
        }
    }

    public function update($id, FaqCategoryDTO $dto)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole( ['admin', 'supervisor'])) {
            return ['data' => null, 'message' => 'Unauthorized - admin or supervisor only'];
        }

        DB::beginTransaction();
        try {
            $category = Faq_categories::find($id);
            if (!$category) {
                return ['data' => null, 'message' => 'FAQ category not found'];
            }

            $category->update(['faq_category_name' => $dto->faq_category_name]);
            DB::commit();

            return ['data' => $category, 'message' => 'FAQ category updated successfully'];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('FAQ category update failed', ['error' => $e->getMessage()]);
            return ['data' => null, 'message' => 'Failed to update FAQ category'];
        }
    }

    public function delete($id)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole( ['admin', 'supervisor'])) {
            return ['data' => null, 'message' => 'Unauthorized - admin or supervisor only'];
        }

        DB::beginTransaction();
        try {
            $category = Faq_categories::with('faq')->find($id);
            if (!$category) {
                return ['data' => null, 'message' => 'FAQ category not found'];
            }

            $category->delete();
            DB::commit();

            return ['data' => null, 'message' => 'FAQ category deleted successfully'];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('FAQ category deletion failed', ['error' => $e->getMessage()]);
            return ['data' => null, 'message' => 'Failed to delete FAQ category'];
        }
    }
}
