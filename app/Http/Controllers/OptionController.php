<?php

namespace App\Http\Controllers;

use App\DTO\OptionDto;
use App\DTO\UpdateOptionDto;
use App\Http\Requests\CreateOptionsRequest;
use App\Http\Requests\UpdateOptionsRequest;
use App\ResponseTrait;
use App\Services\OptionService;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    use ResponseTrait;
    protected $optionService;
    public function __construct(OptionService $optionService)
    {
        $this->optionService = $optionService;
    }
    public function create_option(CreateOptionsRequest $request)
    {
        $validated_data = OptionDto::fromArray($request->validated());
        $data = $this->optionService->create_option($validated_data);
        return  $this->Success($data['data'], $data['message']);
    }
    public function update_option(UpdateOptionsRequest $request,$option_id)
    {
        $validated_data = UpdateOptionDto::fromArray($request->validated());
        $data = $this->optionService->update_option($validated_data,$option_id);
        return  $this->Success($data['data'], $data['message']);
    }

    public function delete_option($option_id)
    {
        $data = $this->optionService->delete_option($option_id);
        return  $this->Success($data['data'], $data['message']);
    }
    public function show_option_by_question($question_id)
    {
        $data = $this->optionService->show_option_by_question($question_id);
        return  $this->Success($data['data'], $data['message']);
    }
}
