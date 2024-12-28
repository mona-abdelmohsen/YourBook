<?php

namespace App\Http\Controllers\API;

use App\Models\Auth\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class UserInteractionController
{
    use ApiResponse;

    /**
     * @param $user_id
     * @return JsonResponse
     */
    public function followAccount($user_id): JsonResponse
    {
        $target = User::find($user_id);
        if($target){
            auth()->user()->follow($target);
            return $this->emptySuccessOk();
        }

        return $this->error("Account Not Found!", [
            'user_id'   => ['Account Not Found!',]
        ], self::$responseCode::HTTP_NOT_FOUND);
    }

    /**
     * @param $user_id
     * @return JsonResponse
     */
    public function unfollowAccount($user_id): JsonResponse
    {
        $target = User::find($user_id);
        if($target){
            auth()->user()->unfollow($target);
            return $this->emptySuccessOk();
        }


        return $this->error("Account Not Found!", [
            'user_id'   => ['Account Not Found!',]
        ], self::$responseCode::HTTP_NOT_FOUND);
    }
}
