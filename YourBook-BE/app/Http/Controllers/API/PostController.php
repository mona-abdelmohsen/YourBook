<?php

namespace App\Http\Controllers\API;

use App\Enum\PrivacyEnum;
use App\Enums\SettingName;
use App\Enums\SettingValue;
use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Posts\Mood;
use App\Models\Posts\Post;
use App\Notifications\ReactOnMyPost;
use App\Repository\PostRepositoryInterface;
use App\Traits\ApiResponse;
use App\Traits\MediaHelper;
use DevDojo\LaravelReactions\Models\Reaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use League\CommonMark\CommonMarkConverter;

class PostController extends Controller
{
    use ApiResponse, MediaHelper;

    public function getReactions($post_id): JsonResponse
    {
        $validator = Validator::make(['post_id' => $post_id], [
            'post_id'   => [
                'required', Rule::exists('posts', 'id')
            ]
        ]);

        if($validator->fails()){
            return $this->error($validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $reactions = Post::with([
            'reactions' => function($query){
                return $query->join('users', 'users.id', 'responder_id')
                    ->select([
                        'users.name', 'users.avatar', 'users.email',
                        'reactions.name AS reaction',
                    ]);
            }
        ])->find($post_id)->reactions->map(function($item){
            $item->avatar = url('storage/avatars/'.basename($item->avatar));
            return $item->only(['name', 'avatar', 'email', 'reaction']);
        })->groupBy('reaction');

        return $this->success('success', $reactions, self::$responseCode::HTTP_OK);
    }

    // public function react(Request $request, $post_id): JsonResponse
    // {
    //     $validator = Validator::make(array_merge(request()->toArray(), ['post_id' => $post_id]), [
    //         'reaction' => 'required|string',
    //         'post_id'   => [
    //             'required', Rule::exists('posts', 'id')
    //                 ->whereNull('deleted_at')
    //         ]
    //     ]);

    //     if($validator->fails()){
    //         return $this->error($validator->errors()->first(),
    //             $validator->errors(),
    //             ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    //     }

    //     $post = Post::find($post_id);
    //     $owner = User::find($post->user_id);

    //     if($post->user_id != auth()->id() && $post->privacy == 'private'){
    //         return $this->error('can not react to private post', null, self::$responseCode::HTTP_UNAUTHORIZED);
    //     }
    //     $reaction = Reaction::firstOrNew(['name' => $request->reaction]);
    //     $reaction->save();

    //     auth()->user()->reactTo($post, $reaction);

    //     if($post->reacted() && $owner->id != auth()->id()){
    //         $owner->notify(new ReactOnMyPost(auth()->user(), $post, $request->reaction));
    //     }

    //     return $this->success('success', null, self::$responseCode::HTTP_CREATED);
    // }

    public function react(Request $request, $post_id): JsonResponse 
{
    $validator = Validator::make(array_merge(request()->toArray(), ['post_id' => $post_id]), [
        'reaction' => 'nullable|string',
        'post_id'  => [
            'required', Rule::exists('posts', 'id')->whereNull('deleted_at')
        ]
    ]);

    if ($validator->fails()) {
        return $this->error(
            $validator->errors()->first(),
            $validator->errors(),
            ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    $post = Post::find($post_id);
    $owner = User::find($post->user_id);
    $user = auth()->user();

    if ($post->user_id != auth()->id() && $post->privacy == 'private') {
        return $this->error('Cannot react to private post', null, self::$responseCode::HTTP_UNAUTHORIZED);
    }

    if ($request->filled('reaction')) {
        // Add reaction - Without mass assignment
        $reaction = Reaction::firstOrNew(['name' => $request->reaction]);

        // Instead of filling the model, directly set the 'name' and save
        $reaction->name = $request->reaction;
        $reaction->save();

        // Attach the reaction to the post
        $user->reactTo($post, $reaction);

        if ($post->reacted() && $owner->id != auth()->id()) {
            $owner->notify(new ReactOnMyPost(auth()->user(), $post, $request->reaction));
        }

        return $this->success('Reaction added successfully', null, self::$responseCode::HTTP_CREATED);
    } else {
        // Remove reaction if no reaction provided
        if ($user->hasReaction($post)) {        
            $user->deleteReaction($post);        
            return $this->success('Reaction removed successfully', null, self::$responseCode::HTTP_OK);
        }

        return $this->error('No existing reaction to remove', null, self::$responseCode::HTTP_BAD_REQUEST);
    }
}


    /**
     * @param $post_id
     * @param PostRepositoryInterface $postRepository
     * @return JsonResponse
     */
    public function show($post_id, PostRepositoryInterface $postRepository): JsonResponse
    {
        $post = $postRepository->getPost(post_id: $post_id);
        if (!$post) {
            // Return error if post is not found
            return $this->error(__('validation.custom.post_not_found'), null, self::$responseCode::HTTP_NOT_FOUND);
        }
        return $this->success(__('validation.custom.success'), $post, self::$responseCode::HTTP_OK);
    }

    /**
     * @param PostRepositoryInterface $postRepository
     * @return JsonResponse
     */
    public function index(PostRepositoryInterface $postRepository): JsonResponse
    {
        $user_id = request()->user_id;
        return $this->success('success', $postRepository->getPosts(user_id: $user_id), self::$responseCode::HTTP_OK);
    }
    //updated

    public function store(Request $request, PostRepositoryInterface $postRepository): JsonResponse
{
    $validator = Validator::make(request()->toArray(), [
        'content'   => 'required_without_all:images,videos,audios,share_link',
        'share_id'  => 'nullable|exists:posts,id',
        'show_in_feed'  => 'required|in:0,1',
        'privacy'   => [
            'required',
            Rule::enum(PrivacyEnum::class),
        ],
        'mood_id'   => 'nullable|exists:moods,id',
        'location'  => 'nullable|string',
        'share_link'    => 'nullable|url:http,https|active_url',
        'images.*'     => 'image|max:'.env('MAX_UPLOAD_FILE_SIZE'),
        'videos.*' => 'nullable|file|mimes:mp4,mov,ogg,qt',
        'audios.*'       => 'nullable|file|mimes:mp3,wav|max:10240',
        'content_background'    => 'nullable|string',
        'tagged_friends.*'    => 'exists:users,id',
        'tagged_books.*'    => [
            Rule::exists('books', 'id')
                ->where('user_id', auth()->id())
        ],
    ]);

    if ($validator->fails()) {
        return $this->error($validator->errors()->first(),
            $validator->errors(),
            ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }

    $content = $request->input('content'); 

    if (!is_null($content) && $content !== '') {
        // Convert the content to CommonMarkConverter if it's not null/empty
        $content = (new CommonMarkConverter())->convert($content);
    } else {
        $content = '';
    }

    // Create Post Model
    $post = Post::create(
        array_merge(
            $request->only([
                'mood_id', 'location', 'share_link',
                'privacy', 'show_in_feed', 'content_background',
                'share_id',
            ]),
            [
                'user_id'       => auth()->id(),
                'content'       => $content,  // Save the processed content here
                'created_at'    => now(),
                'updated_at'    => now(),
            ]
        )
    );

    /** Upload Images and Video files  */
    $this->mediaHandler($post, 'images');
    $this->mediaHandler($post, 'videos');
    $this->mediaHandler($post, 'audios');

    /** Tagged Friends  */
    if (request()->has('tagged_friends')) {
        $authUser = auth()->user();
        $taggedFriends = collect($request->tagged_friends);
        $errors = [];

        $filteredTaggedFriends = $taggedFriends->filter(function ($friendId) use ($authUser, &$errors) {
            $friend = User::find($friendId);

            if (!$friend) {
                $errors[] = "User with ID $friendId does not exist.";
                return false; // Skip invalid users
            }

            $mentionSetting = $friend->settings()
                ->where('setting_name', SettingName::MentionMe->value)
                ->first();

            if (!$mentionSetting) {
                return true;
            }

            switch ($mentionSetting->setting_value) {
                case SettingValue::All->value:
                    return true; // Allow mention for everyone
                case SettingValue::MyFriends->value:
                    if ($friend->isFriendWith($authUser)) {
                        return true;
                    }
                    $errors[] = "You are not friends with User ID $friendId and cannot mention them.";
                    return false;
                case SettingValue::FriendsOfFriends->value:
                    if ($friend->isFriendOfFriend($authUser)) {
                        return true;
                    }
                    $errors[] = "You are not a friend of a friend of User ID $friendId and cannot mention them.";
                    return false;
                default:
                    $errors[] = "User with ID $friendId has an invalid mention setting.";
                    return false;
            }
        });

        if ($errors) {
            return $this->error("Some users could not be tagged.", ['errors' => $errors], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Sync only the filtered friends
        $post->taggedFriends()->sync($filteredTaggedFriends);
    }

    /** Tagged Books */
    if (request()->has('tagged_books')) {
        $post->taggedBooks()->sync($request->tagged_books);
    }

    return $this->success("success", $postRepository->getPost($post->id), self::$responseCode::HTTP_OK);
}


/**
 * Validate tagged friends for the post.
 *
 * @param array $taggedFriends
 * @return array Validation errors, if any.
 */
private function validateTaggedFriends(array $taggedFriends): array
{
    $errors = [];
    $authUser = auth()->user();

    foreach ($taggedFriends as $friendId) {
        $friend = User::find($friendId);
        if (!$friend) {
            $errors[] = "User with ID $friendId does not exist.";
            continue;
        }

        $mentionSetting = $friend->settings()->where('setting_name', SettingName::MentionMe->value)->first();
        if ($mentionSetting) {
            $canMention = match ($mentionSetting->setting_value) {
                SettingValue::All->value => true,
                SettingValue::MyFriends->value => $friend->isFriendWith($authUser),
                SettingValue::FriendsOfFriends->value => $friend->isFriendOfFriend($authUser),
                default => false,
            };

            if (!$canMention) {
                $errors[] = "Cannot mention User ID $friendId due to their settings.";
            }
        }
    }

    return $errors;
}

    

    /**
     * @return JsonResponse
     */
    public function getMoods(): JsonResponse
    {
        $moods = Mood::all();
        $moods = $moods->groupBy('activity')
            ->map(function ($items) {
                $moods = $items->map(function ($item) {
                    return [
                        'id'   => $item['id'],
                        'mood' => $item['mood'],
                        'mood_pic' => asset($item['mood_pic'])
                    ];
                })->all();

                $firstItem = $items->first();

                return [
                    'activity' => $firstItem['activity'],
                    'activity_pic' => asset($firstItem['activity_pic']),
                    'activity_description' => $firstItem['activity_description'],
                    'moods' => $moods
                ];
            })->values()->all();

        return $this->success('success', $moods, self::$responseCode::HTTP_OK);
    }

    public function destroy($post_id): JsonResponse
    {
        $validator = Validator::make(array_merge(request()->toArray(), ['post_id' => $post_id]), [
            'post_id'   => [
                'required', Rule::exists('posts', 'id')
                    ->where('user_id', auth()->id())
            ]
        ]);

        if($validator->fails()){
            return $this->error($validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        Post::find($post_id)->delete();

        return $this->success('success', null, self::$responseCode::HTTP_OK);
    }


    public function feed(PostRepositoryInterface $postRepository): JsonResponse
    {
        return $this->success('success', $postRepository->getPostsFeed(), self::$responseCode::HTTP_OK);
    }
}
