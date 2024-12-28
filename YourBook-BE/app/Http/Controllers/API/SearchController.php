<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Repository\PostRepositoryInterface;
use App\Traits\ApiResponse;
use App\Traits\Mapping;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Tags\Tag;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SearchController extends Controller
{
    use ApiResponse, Mapping;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function hashtags(Request $request): JsonResponse
    {
        $validator = $this->validateSearchField('search_text');
        if($validator->fails()){
            return $this->error($validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $tags = Tag::where('name', 'LIKE', '%'.$request->search_text.'%')
            ->paginate(20);

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
     * @param Request $request
     * @return JsonResponse
     */
    public function users(Request $request): JsonResponse
    {
        $validator = $this->validateSearchField('name');
        if($validator->fails()){
            return $this->error($validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $users = User::where('id', '!=', auth()->id())
            ->where(function($query)use($request){
                $query->where('country_id', auth()->user()->country_id)
                    ->orWhere('name', 'LIKE', '%'.$request->name.'%');
            })->orderBy('created_at', 'desc')->paginate(20);

        $users->getCollection()->transform(function($user){
                return [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'username'  => $user->username,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'privacy' => $user->privacy,
                    'active-status' => $user->active_status,
                    'avatar'    => $user->avatar? url('storage/avatars/'.basename($user->avatar)): null,
                    'isFriend'  => auth()->user()->isFriendWith($user),
                    'isFollowing' => auth()->user()->isFollowing($user),
                ];
            });

        return $this->success('success', $users, self::$responseCode::HTTP_OK);
    }

    public function posts(Request $request, PostRepositoryInterface $postRepository): JsonResponse
    {
        $validator = $this->validateSearchField('search_text');
        if($validator->fails()){
            return $this->error($validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->success('success', $postRepository->searchContent($request->search_text), self::$responseCode::HTTP_OK);
    }

    /**
     * @param $field
     * @return \Illuminate\Validation\Validator
     */
    private function validateSearchField($field): \Illuminate\Validation\Validator
    {
        return Validator::make(request()->toArray(), [
            $field  => 'required|string',
        ]);
    }

}
