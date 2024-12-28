<?php

namespace App\Http\Controllers\API;

use App\Enum\PrivacyEnum;
use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Posts\Post;
use App\Models\Stories\Story;
use App\Notifications\ReactOnMyStory;
use App\Repository\PostRepositoryInterface;
use App\Repository\StoryRepositoryInterface;
use App\Traits\ApiResponse;
use App\Traits\Mapping;
use App\Traits\MediaHelper;
use DevDojo\LaravelReactions\Models\Reaction;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class StoryController extends Controller
{
    use ApiResponse, MediaHelper, Mapping;

    public function store(Request $request, StoryRepositoryInterface $storyRepository): JsonResponse
    {
        $validator = Validator::make(request()->toArray(), [
            'content'   => 'required_without_all:images,videos,audios',
            'privacy'   => [
                'required',
                Rule::enum(PrivacyEnum::class),
            ],
            'images.*'     => 'image|max:'.env('MAX_UPLOAD_FILE_SIZE'),
//            'videos.*'     => 'video|duration_max:'.env('MAX_VIDEO_FILE_DURATION').'|max:'.env('MAX_UPLOAD_FILE_SIZE'),
            'videos.*'     => 'nullable',
            'audios.*'       => 'nullable',
            'content_background'    => 'nullable|string',
        ]);

        if($validator->fails()){
            return $this->error($validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }


        // Create Post Model
        $story = Story::create(
            array_merge(
                $request->only([
                    'content', 'privacy', 'content_background',
                ]),
                [
                    'user_id'       => auth()->id(),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]
            )
        );

        /** Upload Images and Video files  */
        $mediaArray = ['images', 'videos', 'audios'];
        foreach($mediaArray as $type){
            $this->mediaHandler($story, $type);
        }

        return $this->success("success", $storyRepository->getStory($story->id), self::$responseCode::HTTP_OK);
    }

    /**
     * @param StoryRepositoryInterface $storyRepository
     * @return JsonResponse
     */
    public function index(StoryRepositoryInterface $storyRepository): JsonResponse
    {
        $user_id = request()->user_id;
        return $this->success('success', $storyRepository->getStories(user_id: $user_id), self::$responseCode::HTTP_OK);
    }


    /**
     * @param $story_id
     * @param StoryRepositoryInterface $storyRepository
     * @return JsonResponse
     */
    public function show($story_id, StoryRepositoryInterface $storyRepository): JsonResponse
    {
        $story = $storyRepository->getStories(story_id: $story_id);
        return $this->success('success', $story, self::$responseCode::HTTP_OK);
    }


    public function react(Request $request, $story_id): JsonResponse
    {
        $validator = Validator::make(array_merge(request()->toArray(), ['story_id' => $story_id]), [
            'reaction' => 'required|string',
            'story_id'   => [
                'required', Rule::exists('stories', 'id')
            ]
        ]);

        if($validator->fails()){
            return $this->error($validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $story = Story::find($story_id);
        $owner = User::find($story->user_id);

        if($story->user_id != auth()->id() && $story->privacy == 'private'){
            return $this->error('can not react to private story', null, self::$responseCode::HTTP_UNAUTHORIZED);
        }
        $reaction = Reaction::firstOrNew(['name' => $request->reaction]);
        $reaction->save();

        auth()->user()->reactTo($story, $reaction);

        if($story->user_id != auth()->id()){
            $story->views()->syncWithoutDetaching([auth()->id()]);
        }

        if($story->reacted() && $owner->id != auth()->id()){
            $owner->notify(new ReactOnMyStory(auth()->user(), $story, $request->reaction));
        }

        return $this->success('success', null, self::$responseCode::HTTP_CREATED);
    }

    public function getReactions($story_id): JsonResponse
    {
        $validator = Validator::make(['story_id' => $story_id], [
            'story_id'   => [
                'required', Rule::exists('stories', 'id')
            ]
        ]);

        if($validator->fails()){
            return $this->error($validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $reactions = Story::with([
            'reactions' => function($query){
                return $query->join('users', 'users.id', 'responder_id')
                    ->select([
                        'users.name', 'users.avatar', 'users.email',
                        'reactions.name AS reaction',
                    ]);
            }
        ])->find($story_id)->reactions->map(function($item){
            $item->avatar = url('storage/avatars/'.basename($item->avatar));
            return $item->only(['name', 'avatar', 'email', 'reaction']);
        })->groupBy('reaction');

        return $this->success('success', $reactions, self::$responseCode::HTTP_OK);
    }

    public function markViewed($story_id): JsonResponse
    {
        $validator = Validator::make(array_merge(request()->toArray(), ['story_id' => $story_id]), [
            'story_id'   => [
                'required', Rule::exists('stories', 'id')
            ]
        ]);

        if($validator->fails()){
            return $this->error($validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $story = Story::find($story_id);
        if($story && $story->user_id != auth()->id()){
            $story->views()->syncWithoutDetaching([auth()->id()]);
        }

        return $this->success('success', null, self::$responseCode::HTTP_CREATED);
    }

    public function destroy($story_id): JsonResponse
    {
        $validator = Validator::make(array_merge(request()->toArray(), ['story_id' => $story_id]), [
            'story_id'   => [
                'required', Rule::exists('stories', 'id')
                    ->where('user_id', auth()->id())
            ]
        ]);

        if($validator->fails()){
            return $this->error($validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        Story::find($story_id)->delete();

        return $this->success('success', null, self::$responseCode::HTTP_OK);
    }


    public function feed(): JsonResponse
    {
        $friends = auth()->user()->getFriendsQueryBuilder()
            ->with(['stories.media', 'stories.user', 'stories.views'])
            ->has('stories')
            ->get()->map(function($friend){
                $storiesMapped = $friend->stories->map([$this, 'storyMap']);

                return [
                    ...$friend->only(['id', 'name', 'username', 'email', 'phone', 'privacy']),
                    'avatar'    => $friend->avatar ? url('storage/avatars/'.basename($friend->avatar)): null,
                    'stories_count' => $friend->stories->count(),
                    'stories_viewed'    => $storiesMapped->filter(fn($story) => $story['viewed_at'])->count(),
                    'stories'   => $storiesMapped,
                ];
            })->sortByDesc(function($item){
                return $item['stories']->last()['created_at'];
            })->sortByDesc(fn ($item) => $item['stories_count'] - $item['stories_viewed'] == 0? 0: 1);

        return $this->success('success', $friends, self::$responseCode::HTTP_OK);
    }



}
