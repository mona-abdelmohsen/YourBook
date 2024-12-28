<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

trait ApiResponse
{
    static ResponseAlias $responseCode;

    public function __construct()
    {
        self::$responseCode = new ResponseAlias;
    }

    /**
     * @param $message
     * @param $originalData
     * @param $statusCode
     * @return JsonResponse
     */
    public function success($message, $originalData, $statusCode): JsonResponse
    {
        $message = $message == '' || $message == null ? 'Success': $message;
        $responseData = [
            'success'       => $statusCode >= 200 && $statusCode < 300,
            'status_code'   => $statusCode,
            'data'          => $originalData,
            'message'       => $message,
            'errors'        => null,
        ];
        return response()->json($responseData, $statusCode);
    }

    /**
     * @param $message
     * @param $originalData
     * @param $statusCode
     * @return JsonResponse
     */
    public function error($message, $originalData, $statusCode): JsonResponse
    {
        $responseData = [
            'success'       => $statusCode >= 200 && $statusCode < 300,
            'status_code'   => $statusCode,
            'data'          => null,
            'message'       => $message,
            'errors'        => $originalData,
        ];
        return response()->json($responseData, $statusCode);
    }

    public function emptySuccessOk(): JsonResponse
    {
        return $this->success("Success", null, self::$responseCode::HTTP_OK);
    }

}
