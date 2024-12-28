<?php

namespace App\Http\Controllers\API;

use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserCategoryController
{
    use ApiResponse;

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = auth()->user()->categories;
        return $this->success("Success", $categories, self::$responseCode::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validation
        $validator = Validator::make($request->toArray(), [
            'title'         => 'required|string',
        ]);

        if($validator->fails()){
            return $this->error($validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Store
        $category = auth()->user()->categories()->create([
            'title' => $request->title,
        ]);

        // return Results
        return $this->success('Category Created.', $category, ResponseAlias::HTTP_CREATED);
    }
}
