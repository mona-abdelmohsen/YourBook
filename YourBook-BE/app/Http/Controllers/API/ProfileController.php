<?php

namespace App\Http\Controllers\API;

use App\Enum\PrivacyEnum;
use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Repository\PostRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Traits\ApiResponse;
use App\Traits\Mapping;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    use ApiResponse, Mapping;

    /**
     * @param $user_id
     * @param PostRepositoryInterface $postRepository
     * @return JsonResponse
     */
    public function posts($user_id, PostRepositoryInterface $postRepository): JsonResponse
    {
        $user = User::find($user_id);
        if($user && $this->canViewProfile($user)){
            return $this->success('success',
                $postRepository->profilePosts($user_id)
                , self::$responseCode::HTTP_OK);
        }

        return $this->error('invalid user id', null, self::$responseCode::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @param $user_id
     * @param UserRepositoryInterface $userRepository
     * @return JsonResponse
     */
    public function getGeneralInfo($user_id, UserRepositoryInterface $userRepository): JsonResponse
    {
        $user = User::find($user_id);
        if($user && $this->canViewProfile($user)){
            return $this->success("success", $userRepository->getAccountGeneralInfo($user_id), self::$responseCode::HTTP_OK );
        }
        return $this->error('invalid user id', null, self::$responseCode::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @param $user_id
     * @return JsonResponse
     */
    public function books($user_id): JsonResponse
    {
        $user = User::find($user_id);
        if($user && $this->canViewProfile($user)){
            $areFriends = $user->isFriendWith(auth()->user());

            $data = $user->books()->where(function($query)use($areFriends){
                $query->whereNull('parent_id');
                if($areFriends){
                    $query->where('privacy', '!=', PrivacyEnum::PRIVATE);
                }else{
                    $query->where('privacy', PrivacyEnum::PUBLIC);
                }
            })->orderBy('books.created_at', 'desc')->with(['mediaFiles', 'children'])->paginate($per_page ?? 15);
            $responseData = $data->getCollection()->transform(function($book){
                $media = $book->mediaFiles->map([$this, 'mediaMap'])->keyBy('uuid')->toArray();
                $media = array_values($media);
                $book->is_favorite = $book->isFavoritedBy(auth()->user());
                $book = $book->toArray();
                unset($book['media_files']);
                unset($book['_lft']);
                unset($book['_rgt']);
                $book['media'] = $media;
                return $book;
            });

            $paginatedTransformedData = new \Illuminate\Pagination\LengthAwarePaginator(
                $responseData,
                $data->total(),
                $data->perPage(),
                $data->currentPage(),
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );

            return $this->success("success", $paginatedTransformedData, self::$responseCode::HTTP_OK);
        }

        return $this->error('invalid user id', null, self::$responseCode::HTTP_UNPROCESSABLE_ENTITY);
    }


    private function canViewProfile(User $user): bool
    {
        return auth()->id() != $user->id && !auth()->user()->isBlockedBy($user);
    }

}
