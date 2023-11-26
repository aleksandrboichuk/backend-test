<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetCompaniesRequest;
use App\Interfaces\ResponseInterface;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class CompaniesController extends Controller
{

    public function __construct(protected ResponseInterface $responseService){}

    /**
     * Returns All companies with pagination
     *
     * @param GetCompaniesRequest $request
     * @return JsonResponse
     */
    public function getAll(GetCompaniesRequest $request): JsonResponse
    {
        try{

            $data = (new Company())->getAll(
                (int)$request->get('perPage'),
                    Arr::except($request->validated(), ['perPage']) // removing unnecessary field from validated array
                );

            return $this->responseService->success($data->toArray(), 'companies');

        }catch (\Exception $exception){

            return $this->responseService->failed($exception->getMessage());
        }

    }
}
