<?php

namespace App\Services;



use App\Models\Posts\Comment;
use Illuminate\Http\Request;

interface CommentServiceInterface
{
    /**
     * Handles creating a new comment for given model.
     * @return mixed the configured comment-model
     */
    public function store(Request $request): mixed;

    /**
     * Handles updating the message of the comment.
     * @return mixed the configured comment-model
     */
    public function update(Request $request, Comment $comment): mixed;

    /**
     * Handles deleting a comment.
     * @param Comment $comment
     * @return void
     */
    public function destroy(Comment $comment): void;

    /**
     * Handles creating a reply "comment" to a comment.
     * @return mixed the configured comment-model
     */
    public function reply(Request $request, Comment $comment): mixed;
}
