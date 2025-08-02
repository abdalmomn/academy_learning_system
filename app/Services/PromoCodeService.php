<?php

namespace App\Services;

use App\DTO\PromoCodeDto;
use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use function PHPUnit\Framework\isEmpty;

class PromoCodeService
{
    public function create_promo_code(PromoCodeDto $promoCodeDto)
    {
        $user = Auth::user();
        if (!$user->hasRole('supervisor')){
            return [
                'data' => null,
                'message' => 'must be a supervisor to update a course requirement'
            ];
        }
        try {
        $data = (array)$promoCodeDto;
        $data['promo_code'] = 'C-'.strtoupper(Str::random(8));
        $promo_code = PromoCode::query()->create($data);

        $user = User::query()
            ->where('id', $promoCodeDto->teacher_id)
            ->first();
        if (!$user->hasRole('teacher')){
            return [
                'data' => null,
                'message' => 'the promo code owner must be a teacher'
            ];
        }

        Log::info('promo code created successfully', [
            'promo id' => $promo_code->id,
            'promo code' => $promo_code->promo_code
        ]);

            return [
                'data' => $promo_code,
                'message' => 'promo code created successfully'
            ];
        } catch (\Exception $e) {
            Log::error('fail to create promo code', [
                'error' => $e->getMessage(),
            ]);

            return [
                'data' => null,
                'message' => 'fail to create promo code'
            ];
        }
    }

    public function show_my_promo_codes():array
    {
        $user = Auth::user();
        if (!$user->hasRole('teacher')){
            return [
                'data' => null,
                'message' => 'must be a teacher'
            ];
        }
        $promo_codes = PromoCode::query()
            ->where('teacher_id', Auth::id())
            ->select('id','promo_code','discount_percentage','usage_limit','expires_in')
            ->get();
        if ($promo_codes->isEmpty()){
            return [
                'data' => null,
                'message' => 'there is no promo codes right now'
            ];
        }
        return [
            'data' => $promo_codes,
            'message' => 'retrieved promo codes successfully'
            ];
    }


    public function show_all_promo_codes(): array
    {
        $user = Auth::user();
        if (!$user->hasRole(['supervisor' , 'admin'])){
            return [
                'data' => null,
                'message' => 'must be admin or supervisor to show all promo codes'
            ];
        }
        $promo_codes = PromoCode::query()
            ->select('id','promo_code','discount_percentage','usage_limit','expires_in', 'teacher_id')
            ->get();
        if ($promo_codes->isEmpty()){
            return [
                'data' => null,
                'message' => 'there is no promo codes right now'
            ];
        }
        return [
            'data' => $promo_codes,
            'message' => 'retrieved promo codes successfully'
        ];
    }

    public function delete_promo_code($code_id): array
    {
        $user = Auth::user();
        if (!$user->hasRole(['supervisor' , 'admin'])){
            return [
                'data' => null,
                'message' => 'must be admin or supervisor to delete a promo code'
            ];
        }
        $promo_code = PromoCode::query()->find($code_id);
        if (!$promo_code){
            return [
                'data' => null,
                'message' => 'a promo code not found'
            ];
        }
        $promo_code->delete();
        return [
            'data' => null,
            'message' => 'a promo code deleted successfully'
        ];
    }

    public function delete_all_promo_codes(): array
    {
        $user = Auth::user();
        if (!$user->hasRole(['supervisor' , 'admin'])){
            return [
                'data' => null,
                'message' => 'must be admin or supervisor to delete a promo code'
            ];
        }
        PromoCode::query()->delete();
        return [
            'data' => null,
            'message' => 'all promo codes deleted successfully'
        ];
    }
}
