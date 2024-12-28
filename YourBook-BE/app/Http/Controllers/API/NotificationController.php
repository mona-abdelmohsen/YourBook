<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    use ApiResponse;

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->success('success',
            auth()->user()->notifications()->paginate(20)
            , self::$responseCode::HTTP_OK);
    }


    /**
     * @return JsonResponse
     */
    public function seen(): JsonResponse
    {
        auth()->user()->unreadNotifications->markAsRead();
        return $this->emptySuccessOk();
    }


    /**
     * @return JsonResponse
     */
    public function clear(): JsonResponse
    {
        auth()->user()->notifications()->delete();
        return $this->emptySuccessOk();
    }

}
