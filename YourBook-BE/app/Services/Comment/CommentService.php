<?php

namespace App\Services\Comment;

use App\Models\Posts\Comment;
use App\Services\CommentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CommentService implements CommentServiceInterface
{

    /**
     * Handles creating a new comment for given model.
     * @return mixed the configured comment-model
     */
    public function store(Request $request): mixed
    {
        // Define guest rules if user is not logged in.
        if (!Auth::check()) {
            $guest_rules = [
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|string|email|max:255',
            ];
        }

        // Merge guest rules, if any, with normal validation rules.
        Validator::make($request->all(), array_merge($guest_rules ?? [], [
            'commentable_type' => 'required|string',
            'commentable_id' => 'required|string|min:1',
            'message' => 'required|string'
        ]))->validate();

        $model = $request->commentable_type::findOrFail($request->commentable_id);


        $comment = new Comment;

        if (!Auth::check()) {
            $comment->guest_name = $request->guest_name;
            $comment->guest_email = $request->guest_email;
        } else {
            $comment->commenter()->associate(Auth::user());
        }

        $comment->commentable()->associate($model);
        $comment->comment = $request->message;
        $comment->approved = true;
        $comment->save();

        return $comment;
    }

    /**
     * Handles updating the message of the comment.
     * @return mixed the configured comment-model
     */
    public function update(Request $request, Comment $comment): mixed
    {
        Validator::make($request->all(), [
            'message' => 'required|string'
        ])->validate();

        $comment->update([
            'comment' => $request->message
        ]);

        return $comment;
    }

    /**
     * Handles deleting a comment.
     * @param Comment $comment
     * @return void
     */
    public function destroy(Comment $comment): void
    {
        $comment->delete();
    }

    /**
     * Handles creating a reply "comment" to a comment.
     * @return mixed the configured comment-model
     * @throws ValidationException
     */
    public function reply(Request $request, Comment $comment): mixed
    {
        Validator::make($request->all(), [
            'message' => 'required|string'
        ])->validate();

        $reply = new Comment;
        $reply->commenter()->associate(Auth::user());
        $reply->commentable()->associate($comment->commentable);
        $reply->parent()->associate($comment);
        $reply->comment = $request->message;
        $reply->approved = true;
        $reply->save();

        return $reply;
    }

}
