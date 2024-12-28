<?php

namespace App\Http\Controllers\Utilities;

use App\Http\Controllers\Controller;
use App\Services\DropzoneInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DropzoneController extends Controller
{

    /**
     * @param Request $request
     * @param DropzoneInterface $dropzone
     * @return JsonResponse
     */
    public function handler(Request $request, DropzoneInterface $dropzone){

        $file = $request->file('file');
        $res = $dropzone->store($file);

        return response()->json($res);
    }
}
