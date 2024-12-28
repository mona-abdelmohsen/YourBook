<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Book;
use App\Models\Posts\Post;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use ApiResponse;

    /**
     * @param $item_id
     * @param $item_type
     * @param Request $request
     * @return JsonResponse
     */
    public function report($item_id, $item_type, Request $request): JsonResponse
    {
        switch($item_type){
            case 'post':
                $item = Post::find($item_id);
                break;
            case 'book':
                $item = Book::find($item_id);
                break;
            case 'user':
                $item = User::find($item_id);
                break;
            default:
                return $this->error('you can only report a post, book or a user', null, self::$responseCode::HTTP_UNPROCESSABLE_ENTITY);
        }

        if(!$item){
            return $this->error('this '.$item_type.' not Found', null, self::$responseCode::HTTP_NOT_FOUND);
        }

        $item->report([
            'reason' => $request->reason,
            'meta' => is_array($request->meta) ? $request->meta: [],
        ], auth()->user());

        return $this->success('success', [
            'message'   => 'report Captured'
        ], self::$responseCode::HTTP_CREATED);
    }
}
