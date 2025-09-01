<?php

namespace App\Services;
use App\DTO\FaqDTO;
use App\Models\Faq;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
class FaqService
{
    public function getAll()
    {
        try {
            $faqs = Faq::with('category')->latest()->get();
            return [
                'data' => $faqs,
                'message' => $faqs->isEmpty() ? 'No FAQs found' : 'All FAQs'
            ];
        } catch (Exception $e) {
            Log::error('Fetching FAQs failed', ['error' => $e->getMessage()]);
            return ['data' => null, 'message' => 'Failed to fetch FAQs'];
        }
    }

    public function getById($id)
    {
        try {
            $faq = Faq::with('category')->find($id);
            return [
                'data' => $faq,
                'message' => $faq ? 'FAQ details' : 'FAQ not found'
            ];
        } catch (Exception $e) {
            Log::error('Fetching FAQ failed', ['error' => $e->getMessage()]);
            return ['data' => null, 'message' => 'Failed to fetch FAQ'];
        }
    }

    public function store(FaqDTO $dto)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'supervisor'])) {
            return ['data' => null, 'message' => 'Unauthorized - admin or supervisor only'];
        }

        DB::beginTransaction();
        try {
            $faq = Faq::create([
                'question' => $dto->question,
                'answer'   => $dto->answer,
                'faq_category_id' => $dto->faq_category_id
            ]);

            DB::commit();
            return ['data' => $faq, 'message' => 'FAQ created successfully'];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('FAQ creation failed', ['error' => $e->getMessage()]);
            return ['data' => null, 'message' => 'Failed to create FAQ'];
        }
    }

    public function update($id, FaqDTO $dto)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'supervisor'])) {
            return ['data' => null, 'message' => 'Unauthorized - admin or supervisor only'];
        }

        DB::beginTransaction();
        try {
            $faq = Faq::find($id);
            if (!$faq) {
                return ['data' => null, 'message' => 'FAQ not found'];
            }

            $faq->update([
                'question' => $dto->question,
                'answer'   => $dto->answer,
                'faq_category_id' => $dto->faq_category_id
            ]);

            DB::commit();
            return ['data' => $faq, 'message' => 'FAQ updated successfully'];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('FAQ update failed', ['error' => $e->getMessage()]);
            return ['data' => null, 'message' => 'Failed to update FAQ'];
        }
    }

    public function delete($id)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'supervisor'])) {
            return ['data' => null, 'message' => 'Unauthorized - admin or supervisor only'];
        }

        DB::beginTransaction();
        try {
            $faq = Faq::find($id);
            if (!$faq) {
                return ['data' => null, 'message' => 'FAQ not found'];
            }

            $faq->delete();
            DB::commit();
            return ['data' => null, 'message' => 'FAQ deleted successfully'];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('FAQ deletion failed', ['error' => $e->getMessage()]);
            return ['data' => null, 'message' => 'Failed to delete FAQ'];
        }
    }
}
