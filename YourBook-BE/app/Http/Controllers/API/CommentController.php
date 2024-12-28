<?php

namespace App\Http\Controllers\API;

use App\Enum\PrivacyEnum;
use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Posts\Comment;
use App\Models\Posts\Post;
use App\Notifications\CommentOnMyPost;
use App\Notifications\ReactOnMyComment;
use App\Notifications\ReplyToMyComment;
use App\Traits\ApiResponse;
use App\Traits\Mapping;
use DevDojo\LaravelReactions\Models\Reaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CommentController extends Controller
{

    use ApiResponse;
    use Mapping;

    /**
     * @param Request $request
     * @param $post_id
     * @return JsonResponse
     */
    public function store(Request $request, $post_id): JsonResponse
    {
        $validator = Validator::make(array_merge($request->toArray(), ['post_id' => $post_id]), [
            'comment' => 'nullable|string',
            'post_id' => [
                'required',
                Rule::exists('posts', 'id')
            ],
            'images.*' => 'nullable|image',
            'audios.*' => 'nullable',
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

        if ($post->privacy == PrivacyEnum::FRIENDS && !$owner->isFriendWith(auth()->user())) {
            return $this->error('only friends can comment in this post', null, self::$responseCode::HTTP_UNAUTHORIZED);
        }
        //??
        if ($post->privacy == PrivacyEnum::FRIENDS_OF_FRIENDS && !$owner->isFriendOfFriend(auth()->user())) {
            return $this->error(
                'Only friends of friends can comment on this post.',
                null,
                self::$responseCode::HTTP_UNAUTHORIZED
            );

        }

        if ($post->privacy == PrivacyEnum::PRIVATE && $owner->id != auth()->id()) {
            return $this->error('only author of the post can comment in this post', null, self::$responseCode::HTTP_UNAUTHORIZED);
        }

        $comment = new Comment;
        $comment->commenter()->associate(auth()->user());
        $comment->commentable()->associate($post);
        if ($request->filled('comment')) {
            $comment->comment = $request->input('comment');
        }
        $comment->approved = true;
        $comment->save();

        $this->mediaHandler($comment, 'images');
        $this->mediaHandler($comment, 'audios');


        if ($owner->id != auth()->id()) {
            $owner->notify(new CommentOnMyPost(auth()->user(), $post, $comment));
        }

        return $this->success('success', null, self::$responseCode::HTTP_CREATED);
    }

    /**
     * @param Comment $post
     * @param $target_collection
     * @return JsonResponse
     */
    private function mediaHandler(Comment $post, $target_collection): mixed
    {
        if (!request()->has($target_collection)) {
            return null;
        }

        // upload to server storage ...
        //config(['media-library.disk_name' => 'public']);

        // dispatch new job to compress image and re-store it again ...

        return $post->addMultipleMediaFromRequest([$target_collection])
            ->each(function ($fileAdder) use ($target_collection) {
                $fileAdder->toMediaCollection($target_collection);
            });
    }


    /**
     * @param $post_id
     * @param $comment_id
     * @return JsonResponse
     */

    //delete comment and its replies
    public function destroy($post_id, $comment_id): JsonResponse
{
    $authUserId = auth()->id();
    
    $validator = Validator::make(['comment_id' => $comment_id], [
        'comment_id' => [
            'required',
            Rule::exists('comments', 'id')
                ->where('commenter_id', $authUserId)  
                ->whereNull('deleted_at'), 
        ],
    ]);

    // If validation fails, return an error response
    if ($validator->fails()) {
        return $this->error(
            $validator->errors()->first(),
            $validator->errors(),
            ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    $comment = Comment::find($comment_id);
    $post = Post::find($post_id);
    if(!$post)
    {
        return $this->error(
            'Post not found.',
            null,
            ResponseAlias::HTTP_NOT_FOUND
        );
    }
    if (!$comment) {
        return $this->error(
            'Comment not found.',
            null,
            ResponseAlias::HTTP_NOT_FOUND
        );
    }

    // Check if the authenticated user is the owner of the comment
    if ($comment->commenter_id != $authUserId) {
        return $this->error(
            'You are not authorized to delete this comment.',
            null,
            ResponseAlias::HTTP_UNAUTHORIZED
        );
    }

    // Delete all replies (children)
    $comment->children()->delete(); 

    // Delete the main comment
    $comment->delete(); 

    return $this->success('Comment and its replies deleted successfully.', null, self::$responseCode::HTTP_OK);
}

    /**
     * @param Request $request
     * @param $post_id
     * @param $comment_id
     * @return JsonResponse
     */
    public function reply(Request $request, $post_id, $comment_id): JsonResponse
    {
        $validator = Validator::make(array_merge($request->toArray(), [
            'comment_id' => $comment_id,
            'post_id' => $post_id,
        ]), [
            'reply' => 'nullable|string',
            'post_id' => 'required|exists:posts,id',
            'comment_id' => [
                'required',
                Rule::exists('comments', 'id')
                    ->whereNull('deleted_at')
            ],
            'images.*' => 'nullable|image',
            'audios.*' => 'nullable',
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

        if ($post->privacy == PrivacyEnum::FRIENDS && !$owner->isFriendWith(auth()->user())) {
            return $this->error('only friends can comment in this post', null, self::$responseCode::HTTP_UNAUTHORIZED);
        }
        
        if ($post->privacy == PrivacyEnum::FRIENDS_OF_FRIENDS && !$owner->isFriendOfFriend(auth()->user())) {
            return $this->error('only friends of friends can comment in this post', null, self::$responseCode::HTTP_UNAUTHORIZED);
        }

        if ($post->privacy == PrivacyEnum::PRIVATE && $owner->id != auth()->id()) {
            return $this->error('only author of the post can comment in this post', null, self::$responseCode::HTTP_UNAUTHORIZED);
        }

        $comment = Comment::find($comment_id);

        $reply = new Comment;
        $reply->commenter()->associate(auth()->user());
        $reply->commentable()->associate($comment->commentable);
        $reply->parent()->associate($comment);
        if ($request->filled('reply')) {
            $reply->comment = $request->input('reply');
        }
        
        $reply->approved = true;
        $reply->save();

        $this->mediaHandler($reply, 'images');
        $this->mediaHandler($reply, 'audios');


        if ($comment->commenter->id != auth()->id()) {
            $comment->commenter->notify(new ReplyToMyComment(auth()->user(), $post, $comment, $reply));
        }

        if ($owner->id != auth()->id()) {
            $owner->notify(new CommentOnMyPost(auth()->user(), $post, $reply));
        }


        return $this->success('success', null, self::$responseCode::HTTP_CREATED);
    }


    // Delete reply
    public function deleteReply(Request $request, $reply_id): JsonResponse
    {
        $validator = Validator::make(['reply_id' => $reply_id], [
            'reply_id' => [
                'required',
                Rule::exists('comments', 'id')->whereNull('deleted_at'),
            ],
        ]);
    
        if ($validator->fails()) {
            return $this->error(
                $validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    
        $reply = Comment::find($reply_id);
    
        // Check if the comment is a reply
        if (is_null($reply->child_id)) {
            return $this->error(
                'This comment is not a reply.',
                null,
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
    
        // Check if the authenticated user is the owner of the reply or the post author
        $post = $reply->commentable; 
        $postAuthorId = $post->user_id ?? null; 
        if ($reply->commenter_id !== auth()->id() && $postAuthorId !== auth()->id()) {
            return $this->error(
                'You are not authorized to delete this reply.',
                null,
                ResponseAlias::HTTP_UNAUTHORIZED
            );
        }
    
        $reply->delete();
    
        return $this->success('Reply deleted successfully.', null, self::$responseCode::HTTP_OK);
    }
    



    /**
     * @param $post_id
     * @return JsonResponse
     */
    public function index($post_id): JsonResponse
    {
        $validator = Validator::make(['post_id' => $post_id], [
            'post_id' => [
                'required',
                Rule::exists('posts', 'id')
            ]
        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $comments = Post::find($post_id)->comments()->whereNull('child_id')
            ->orderBy('created_at', 'desc')
            ->with([
                'media',
                'children' => function ($query) {
                    return $query->with('media')->limit(3)->orderBy('created_at', 'desc');
                }
            ])->paginate(request()->per_page ?? 20);

        $comments->getCollection()->transform([$this, 'commentMap']);
        return $this->success('success', $comments, self::$responseCode::HTTP_OK);
    }

    public function show($post_id, $comment_id): JsonResponse
    {
        $validator = Validator::make(['post_id' => $post_id, 'comment_id' => $comment_id], [
            'post_id' => [
                'required',
                Rule::exists('posts', 'id')
            ],
            'comment_id' => [
                'required',
                Rule::exists('comments', 'id')
            ]
        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );
        }


        $comments = Comment::where('child_id', $comment_id)
            ->when(auth()->user(), function ($query) {
                $query->with('myReactions');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(request()->per_page ?? 20);

        $comments->getCollection()->transform([$this, 'commentMap']);

        return $this->success('success', $comments, self::$responseCode::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param $post_id
     * @param $comment_id
     * @return JsonResponse
     */
    public function react(Request $request, $post_id, $comment_id): JsonResponse
    {
        $validator = Validator::make(array_merge(request()->toArray(), [
            'comment_id' => $comment_id,
            'post_id' => $post_id,
        ]), [
            'reaction' => 'required|string',
            'post_id' => 'required',
            Rule::exists('posts', 'id')
                ->whereNull('deleted_at'),
            'comment_id' => [
                'required',
                Rule::exists('comments', 'id')
                    ->whereNull('deleted_at')
            ]
        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $comment = Comment::find($comment_id);

        $reaction = Reaction::firstOrNew(['name' => $request->reaction]);

        auth()->user()->reactTo($comment, $reaction);

        if ($comment->reacted() && $comment->commenter->id != auth()->id()) {
            $comment->commenter->notify(new ReactOnMyComment(auth()->user(), $post_id, $comment, $request->reaction));
        }

        return $this->success('success', null, self::$responseCode::HTTP_CREATED);
    }


    public function getReactions($post_id, $comment_id): JsonResponse
    {
        $validator = Validator::make([
            'post_id' => $post_id,
            'comment_id' => $comment_id,
        ], [
            'post_id' => 'required',
            Rule::exists('posts', 'id')
                ->whereNull('deleted_at'),
            'comment_id' => [
                'required',
                Rule::exists('comments', 'id')
            ]
        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $reactions = Comment::with([
            'reactions' => function ($query) {
                return $query->join('users', 'users.id', 'responder_id')
                    ->select([
                        'users.name',
                        'users.avatar',
                        'users.email',
                        'reactions.name AS reaction',
                    ]);
            }
        ])->find($comment_id)->reactions->map(function ($item) {
            $item->avatar = url('storage/avatars/' . basename($item->avatar));
            return $item->only(['name', 'avatar', 'email', 'reaction']);
        })->groupBy('reaction');

        return $this->success('success', $reactions, self::$responseCode::HTTP_OK);
    }





}
