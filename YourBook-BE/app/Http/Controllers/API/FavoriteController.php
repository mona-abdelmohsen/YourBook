<?php

namespace App\Http\Controllers\API;

use App\Enum\PrivacyEnum;
use App\Enums\SettingName;
use App\Enums\SettingValue;
use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Book;
use App\Models\Posts\Post;
use App\Repository\PostRepositoryInterface;
use App\Traits\ApiResponse;
use App\Traits\Mapping;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class FavoriteController extends Controller
{
    use ApiResponse, Mapping;

    /**
     * @param $item_id
     * @param $item_type
     * @return JsonResponse
     */
    public function favorite($item_id, $item_type): JsonResponse
    {
        switch($item_type)
        {
            case 'post':
                $item = Post::find($item_id);
                break;
            case 'book':
                $item = Book::find($item_id);
                break;
            default:
                return $this->error('you can only add post or book to favorite', null, self::$responseCode::HTTP_UNPROCESSABLE_ENTITY);
        }
        if(!$item){
            return $this->error('this '.$item_type.' not Found', null, self::$responseCode::HTTP_NOT_FOUND);
        }
        auth()->user()->toggleFavorite($item);
        return $this->emptySuccessOk();
    }

    /**
     * @param $item_type
     * @param PostRepositoryInterface $postRepository
     * @return JsonResponse
     */

public function index($item_type, PostRepositoryInterface $postRepository)
{
    switch ($item_type){
        case 'post':
            return $this->success('success', $postRepository->favorites(), self::$responseCode::HTTP_OK);

        case 'book':
            $data = auth()->user()->favorites(Book::class)
                ->orderBy('books.created_at', 'desc')
                ->with(['mediaFiles', 'children'])->paginate($per_page ?? 15);

            $responseData = $data->getCollection()->transform(function($book){
                $media = $book->mediaFiles->map([$this, 'mediaMap'])->keyBy('uuid')->toArray();
                $media = array_values($media);
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

        default:
            $this->error('only posts and books can be in favorite', null, self::$responseCode::HTTP_UNPROCESSABLE_ENTITY);
    }
}


//
private function handleItemType($item_type, $user, $postRepository)
{
    switch ($item_type) {
        case 'post':
            $data = $postRepository->favorites($user);
            return $this->success('Returning favorite posts.', $data, self::$responseCode::HTTP_OK);

        case 'book':
            $data = $user->favorites(Book::class)
                ->orderBy('books.created_at', 'desc')
                ->with(['mediaFiles', 'children'])
                ->paginate(request('per_page', 15));

            $responseData = $data->getCollection()->transform(function ($book) {
                $media = $book->mediaFiles->map([$this, 'mediaMap'])->keyBy('uuid')->values();
                $bookData = $book->toArray();
                unset($bookData['media_files'], $bookData['_lft'], $bookData['_rgt']);
                $bookData['media'] = $media;
                return $bookData;
            });

            return $this->success('Returning favorite books.', [
                'books' => $responseData,
                'pagination' => [
                    'total' => $data->total(),
                    'per_page' => $data->perPage(),
                    'current_page' => $data->currentPage(),
                ]
            ], self::$responseCode::HTTP_OK);

        default:
            return $this->error(
                'Invalid item type. Only "post" or "book" are allowed.',
                null,
                self::$responseCode::HTTP_UNPROCESSABLE_ENTITY
            );
    }
}

public function indexFavouriteByUserId($user_id = null, $item_type, PostRepositoryInterface $postRepository)
{
    // Determine the user based on the provided user_id or default to the authenticated user
    $user = $user_id ? User::find($user_id) : auth()->user();
    $authUser = auth()->user();
    
    // If the user is not found
    if (!$user) {
        return $this->error('User not found', null, self::$responseCode::HTTP_NOT_FOUND);
    }

    // Apply privacy settings for favorites
    $favouriteSetting = $user->settings()->where('setting_name', SettingName::Favourite->value)->first();

    // Check if favorite setting exists and handle accordingly
    if ($favouriteSetting) {
        switch ($favouriteSetting->setting_value) {
            case SettingValue::OnlyMe->value:
                if ($authUser->id !== $user->id) {
                    return $this->error(
                        'This user only allows themselves to view their favorites list.',
                        null,
                        ResponseAlias::HTTP_UNAUTHORIZED
                    );
                }
                break;

            case SettingValue::MyFriends->value:
                if (!$user->isFriendWith($authUser)) {
                    return $this->error(
                        'This user only allows friends to view their favorites list.',
                        null,
                        ResponseAlias::HTTP_UNAUTHORIZED
                    );
                }
                break;

            case SettingValue::FriendsOfFriends->value:
                if (!$user->isFriendOfFriend($authUser)) {
                    return $this->error(
                        'This user only allows friends of friends to view their favorites list.',
                        null,
                        ResponseAlias::HTTP_UNAUTHORIZED
                    );
                }
                break;

            case SettingValue::All->value:
                // No restrictions for "All" setting
                break;

            default:
                return $this->error(
                    'Invalid favorites setting value.',
                    null,
                    ResponseAlias::HTTP_BAD_REQUEST
                );
        }
    } else {
        return $this->handleItemType($item_type, $user, $postRepository);
    }

    return $this->handleItemType($item_type, $user, $postRepository);
}

}
