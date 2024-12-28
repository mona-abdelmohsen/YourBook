<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\UserRepositoryInterface;
use App\Services\AuthServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    use ApiResponse;

    /**
     * @param UserRepositoryInterface $userRepository
     * @return JsonResponse
     */
    public function getGeneralInfo(UserRepositoryInterface $userRepository): JsonResponse
    {
        return $this->success("Success", $userRepository->getAccountGeneralInfo(), self::$responseCode::HTTP_OK );
    }


    /**
     * @param UserRepositoryInterface $userRepository
     * @return JsonResponse
     */
    public function getInterests(UserRepositoryInterface $userRepository): JsonResponse
    {
        return $this->success("Success", $userRepository->getUserInterests(), self::$responseCode::HTTP_OK );
    }

    /**
     * @param Request $request
     * @param AuthServiceInterface $authService
     * @param UserRepositoryInterface $userRepository
     * @return JsonResponse
     */
    public function updateGeneralInfo(Request $request
        , AuthServiceInterface $authService
        , UserRepositoryInterface $userRepository): JsonResponse
    {
        $validator = Validator::make($request->only('form_type'),[
            'form_type'     => 'required|in:register,update',
        ]);

        if($validator->fails()){
            return $this->error($validator->errors()->first(),
                $validator->errors(),
                self::$responseCode::HTTP_UNPROCESSABLE_ENTITY);
        }

        if($request->form_type == 'register'){
            $validator = Validator::make($request->all(), [
                'name'          => 'string|required|min:5',
                // 'country_id'    => 'integer|required',
                'dial_code'     => 'string|required',
                'user_country_dial_code' => 'string|required|exists:countries,dial_code',
                'phone'         => 'string|required',
                'birth_date'    => 'date|required',
                'photo'         => 'nullable|sometimes|image',
                'gender'        => 'required',
            ]);

            if($validator->fails()){
                return $this->error($validator->errors()->first(),
                    $validator->errors(),
                    self::$responseCode::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        DB::enableQueryLog();

        $authService->updateGeneralInfo($request->all());

        $userRepository->markAccountAsUpdated();

        $queries = DB::getQueryLog();

        return $this->success("Profile Updated.", null, self::$responseCode::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param UserRepositoryInterface $userRepository
     * @return JsonResponse
     */
    public function updateInterests(Request $request, UserRepositoryInterface $userRepository): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'interests' => 'required',
        ]);

        if($validator->fails()){
            return $this->error($validator->errors()->first(),
                $validator->errors(),
                self::$responseCode::HTTP_UNPROCESSABLE_ENTITY);
        }

        $userRepository->updateUserInterests($request->interests);

        return $this->success("Interests Updated", null, self::$responseCode::HTTP_OK);
    }

    /**
     * @param UserRepositoryInterface $userRepository
     * @return JsonResponse
     */
    public function getTopAccounts(UserRepositoryInterface $userRepository): JsonResponse
    {
        return $this->success("Success", $userRepository->getTopAccounts(), self::$responseCode::HTTP_OK);
    }


}
