<?php

namespace App\Http\Controllers\API;

use App\Enums\SettingName;
use App\Enums\SettingValue;
use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Notifications\FriendRequestAccepted;
use App\Notifications\NewFriendRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Multicaret\Acquaintances\Interaction;
use Multicaret\Acquaintances\Status;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class FriendshipController extends Controller
{

    use ApiResponse;

    private function getAuthUser()
    {
        return $this->getUser(auth()->id());
    }

    private function getUser($user_id)
    {
        return User::find($user_id);
    }

    private function validateUserId($value)
    {
        $validator = Validator::make(array_merge(request()->toArray(), ['user_id' => $value]), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    /**
     * @param $user_id
     * @return JsonResponse
     */
    public function sendFriendRequest($user_id): JsonResponse
    {
        $this->validateUserId($user_id);

        $authUser = $this->getAuthUser();
        $recipient = $this->getUser($user_id);

        /** if already friends */
        if ($authUser->isFriendWith($recipient)) {
            return $this->success(
                "You are already friends",
                null,
                self::$responseCode::HTTP_OK
            );
        }

        /** if already sent request */
        if ($authUser->hasSentFriendRequestTo($recipient)) {
            return $this->success(
                "You are already has sent friend request to this user",
                null,
                self::$responseCode::HTTP_OK
            );
        }

        /** if recipient already has sent friend request */
        if ($authUser->hasFriendRequestFrom($recipient)) {
            $this->authUser->acceptFriendRequest($recipient);
            return $this->emptySuccessOk();
        }

        /** check the blocking status */
        if (!$authUser->befriend($recipient)) {
            return $this->error(
                "You cant send friend request to this user.",
                null,
                self::$responseCode::HTTP_UNAUTHORIZED
            );
        }

        $recipient->notify(new NewFriendRequest($authUser));

        return $this->success(
            "friend request sent.",
            null,
            self::$responseCode::HTTP_OK
        );
    }

    /**
     * @return JsonResponse
     */
    public function getFriendRequests(): JsonResponse
    {
        $friendships = $this->getAuthUser()->getFriendRequests()->pluck(['id']);

        $friendships = Interaction::getFriendshipModelName()::whereIn('id', $friendships)
            ->with('sender')->paginate(20);
        $friendships->getCollection()->transform(function ($item) {
            return [
                'id' => $item->sender->id,
                'name' => $item->sender->name,
                'email' => $item->sender->email,
                'phone' => $item->sender->phone,
                'avatar' => $item->sender->avatar ? url('storage/avatars/' . basename($item->sender->avatar)) : null
            ];
        });

        return $this->success(
            "success",
            $friendships,
            self::$responseCode::HTTP_OK
        );
    }

    /**
     * @param $sender_id
     * @return JsonResponse
     */
    public function acceptFriendRequest($sender_id): JsonResponse
    {
        $this->validateUserId($sender_id);

        $authUser = $this->getAuthUser();
        $sender = $this->getUser($sender_id);

        if (!$authUser->hasFriendRequestFrom($sender)) {
            return $this->error(
                'you dont have friend request from this user',
                null,
                self::$responseCode::HTTP_UNAUTHORIZED
            );
        }

        $authUser->acceptFriendRequest($sender);

        $sender->notify(new FriendRequestAccepted($authUser));

        return $this->success('success', null, self::$responseCode::HTTP_OK);
    }

    /**
     * @param $sender_id
     * @return JsonResponse
     */
    public function denyFriendRequest($sender_id): JsonResponse
    {
        $this->validateUserId($sender_id);

        $authUser = $this->getAuthUser();
        $sender = $this->getUser($sender_id);

        if (!$authUser->hasFriendRequestFrom($sender)) {
            return $this->error(
                'you dont have friend request from this user',
                null,
                self::$responseCode::HTTP_UNAUTHORIZED
            );
        }

        $authUser->denyFriendRequest($sender);

        return $this->success('success', null, self::$responseCode::HTTP_OK);
    }


    /**
     * @param $user_id
     * @return JsonResponse
     */
    public function unfriend($user_id): JsonResponse
    {
        $this->validateUserId($user_id);

        $authUser = $this->getAuthUser();
        $user = $this->getUser($user_id);

        if (!$authUser->isFriendWith($user)) {
            return $this->error(
                'you are not a friend of this user',
                null,
                self::$responseCode::HTTP_UNAUTHORIZED
            );
        }

        $authUser->unfriend($user);

        return $this->success('success', null, self::$responseCode::HTTP_OK);
    }


    /**
     * @return JsonResponse
     */
    public function getFriends(): JsonResponse
    {
        $search_text = \request()->search_text;

        $authUser = $this->getAuthUser();
        $friends = $authUser->getFriendsQueryBuilder()
            ->when($search_text, function ($query) use ($search_text) {
                $query->where('name', 'LIKE', '%' . $search_text . '%');
            })->paginate(20);

        $friends->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'email' => $item->email,
                'phone' => $item->phone,
                'avatar' => $item->avatar ? url('storage/avatars/' . basename($item->avatar)) : null,
                'is_friend' => true,
                'is_follow' => auth()->user()->isFollowing($item),
            ];
        });

        return $this->success('success', $friends, self::$responseCode::HTTP_OK);
    }

    /**
     * @return JsonResponse
     */
    public function getBlocked(): JsonResponse
    {
        $blockedList = $this->getAuthUser()->findFriendships(Status::BLOCKED, type: 'sender')
            ->with('recipient')
            ->paginate(20);

        $blockedList->getCollection()->transform(function ($item) {
            return [
                'id' => $item->recipient->id,
                'name' => $item->recipient->name,
                'email' => $item->recipient->email,
                'phone' => $item->recipient->phone,
                'avatar' => $item->recipient->avatar ? url('storage/avatars/' . basename($item->recipient->avatar)) : null
            ];
        });

        return $this->success('success', $blockedList, self::$responseCode::HTTP_OK);
    }

    /**
     * @param $user_id
     * @return JsonResponse
     */
    public function blockUser($user_id): JsonResponse
    {
        $this->validateUserId($user_id);

        $user = $this->getUser($user_id);
        $this->getAuthUser()->blockFriend($user);

        return $this->emptySuccessOk();
    }

    /**
     * @param $user_id
     * @return JsonResponse
     */
    public function unblockUser($user_id): JsonResponse
    {
        $this->validateUserId($user_id);

        $user = $this->getUser($user_id);
        $this->getAuthUser()->unblockFriend($user);

        return $this->emptySuccessOk();
    }


    // get follow list
    public function getFollowersList($user_id): JsonResponse
    {
        // Validate the provided user_id
        $this->validateUserId($user_id);

        // Get the authenticated user
        $authUser = auth()->user();

        // Find the user whose followers list is being requested
        $user = User::find($user_id);

        // Check if the user exists
        if (!$user) {
            return $this->error('User not found.', null, ResponseAlias::HTTP_NOT_FOUND);
        }

        // Check if the authenticated user is requesting their own followings
        if ($authUser->id == $user_id) {
            $followings = $authUser->followings()->get();
        } else {
            // If a follow list setting exists, check the value and apply follow list settings
            $followListSetting = $user->settings()->where('setting_name', SettingName::FollowList->value)->first();

            if ($followListSetting) {
                switch ($followListSetting->setting_value) {
                    case SettingValue::OnlyMe->value:
                        if ($authUser->id !== $user->id) {
                            return $this->error(
                                'This user only allows themselves to view their followers list.',
                                null,
                                ResponseAlias::HTTP_UNAUTHORIZED
                            );
                        }
                        break;

                    case SettingValue::MyFriends->value:
                        if (!$user->isFriendWith($authUser)) {
                            return $this->error(
                                'This user only allows friends to view their followers list.',
                                null,
                                ResponseAlias::HTTP_UNAUTHORIZED
                            );
                        }
                        break;

                    case SettingValue::FriendsOfFriends->value:
                        if (!$user->isFriendOfFriend($authUser)) {
                            return $this->error(
                                'This user only allows friends of friends to view their followers list.',
                                null,
                                ResponseAlias::HTTP_UNAUTHORIZED
                            );
                        }
                        break;

                    case SettingValue::All->value:
                        // If the setting is "All", anyone can view the follow list
                        break;

                    default:
                        return $this->error(
                            'Invalid follow list setting value.',
                            null,
                            ResponseAlias::HTTP_BAD_REQUEST
                        );
                }
            }

            $followings = $user->followings()->get();
        }

        // If the followings list is empty
        if ($followings->isEmpty()) {
            return $this->success(
                'You are not following anyone.',
                $followings,
                ResponseAlias::HTTP_OK
            );
        }

        return $this->success(
            'Followings list retrieved successfully.',
            $followings,
            ResponseAlias::HTTP_OK
        );
    }


    //get friends of user_id
    public function getFriendsList($user_id): JsonResponse
    {
        // Validate the provided user_id
        $this->validateUserId($user_id);

        $authUser = auth()->user();

        // Find the user whose friends list is being requested
        $user = User::find($user_id);

        // Check if the user exists
        if (!$user) {
            return $this->error('User not found.', null, ResponseAlias::HTTP_NOT_FOUND);
        }

        // Check if the authenticated user is requesting their own friends list
        if ($authUser->id == $user_id || $user->isFriendWith($authUser)) {
            $friends = $user->getFriends();

            // If the friends list is empty
            if ($friends->isEmpty()) {
                return $this->success(
                    'You have no friends yet.',
                    $friends,
                    ResponseAlias::HTTP_OK
                );
            }

            // Return the friends list
            return $this->success(
                'Friends list retrieved successfully.',
                $friends,
                ResponseAlias::HTTP_OK
            );
        }

        // If the user is not authorized to view this user's friends list
        return $this->error(
            "You are not authorized to view this user's friends list.",
            null,
            ResponseAlias::HTTP_FORBIDDEN
        );
    }


}
