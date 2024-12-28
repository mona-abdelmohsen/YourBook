<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\PostRepositoryInterface;
use App\Traits\ApiResponse;
use App\Traits\Mapping;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Spatie\Tags\Tag;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TagsController extends Controller
{
    use ApiResponse, Mapping;


    /**
     * @return JsonResponse
     */
    public function trend(): JsonResponse
    {
        $tags = Tag::query()->orderBy('count', 'desc')->paginate(20);
        $tags->getCollection()->transform(function($tag){
            return [
                'id'    => $tag->id,
                'tag'   => $tag->name,
                'posts_count'    => $tag->count,
            ];
        });

        return $this->success('success', $tags, self::$responseCode::HTTP_OK);
    }

    /**
     * @param $hash_tag
     * @param PostRepositoryInterface $postRepository
     * @return JsonResponse
     */
    public function posts($hash_tag, PostRepositoryInterface $postRepository): JsonResponse
    {
        $validator = Validator::make(array_merge(request()->toArray(), ['hash_tag' => $hash_tag]), [
            'hash_tag' => 'required|string',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->success('success', $postRepository->getPostsByHashtags([$hash_tag]), self::$responseCode::HTTP_OK);
    }

}
