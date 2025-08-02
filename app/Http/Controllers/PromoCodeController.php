<?php

namespace App\Http\Controllers;

use App\DTO\PromoCodeDto;
use App\Http\Requests\CreatePromoCodeRequest;
use App\ResponseTrait;
use App\Services\PromoCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PromoCodeController extends Controller
{
    use ResponseTrait;
    public $promoCodeService;
    public function __construct(PromoCodeService $promoCodeService)
    {$this->promoCodeService = $promoCodeService;}

    public function create_promo_code(CreatePromoCodeRequest $request)
    {
        $dto = PromoCodeDto::fromArray($request->validated());
        $data = $this->promoCodeService->create_promo_code($dto);
        return $this->Success($data['data'],$data['message']);
    }

    public function show_my_promo_codes()
    {
        $data = $this->promoCodeService->show_my_promo_codes();
        return $this->Success($data['data'],$data['message']);
    }
    public function show_all_promo_codes()
    {
        $data = $this->promoCodeService->show_all_promo_codes();
        return $this->Success($data['data'],$data['message']);
    }
    public function delete_promo_code($code_id)
    {
        $data = $this->promoCodeService->delete_promo_code($code_id);
        return $this->Success($data['data'],$data['message']);
    }
    public function delete_all_promo_codes()
    {
        $data = $this->promoCodeService->delete_all_promo_codes();
        return $this->Success($data['data'],$data['message']);
    }
}
