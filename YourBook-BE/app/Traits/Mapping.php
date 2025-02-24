<?php

namespace App\Traits;

use App\Models\Auth\User;
use App\Models\Book;
use App\Models\Posts\Comment;
use App\Models\Posts\Post;
use App\Models\Stories\Story;
use Chatify\Facades\ChatifyMessenger as Chatify;

trait Mapping
{

    public function tagMap($item): array
    {
        return [
            'id'    => $item->id,
            'tag'   => $item->name_translated,
            'posts_count'    => $item->count,
            //'slug'  => $tag->slug_translated,
        ];
    }

    public function messageMap($item)
    {
        if (isset($item->attachment) && $item->attachment) {
            $attachmentOBJ = json_decode($item->attachment);
            $attachment = $attachmentOBJ->new_name;
            $attachment_title = htmlentities(trim($attachmentOBJ->old_name), ENT_QUOTES, 'UTF-8');
            $ext = pathinfo($attachment, PATHINFO_EXTENSION);
            $attachment_type = in_array($ext, Chatify::getAllowedImages()) ? 'image' : 'file';
            $path = config('chatify.attachments.folder') . '/' . $attachment;
            $item->attachment = [
                'file'      => $attachment,
                'title'  => $attachment_title,
                'type'      => $attachment_type,
                'url'   => Chatify::storage()->url($path)
            ];
        }
        return $item;
    }

    public function contactUserMap($item): array
    {
        $message = $this->messageMap($item);
        $data = $this->userMap($item);
        return [
            ...$data,
            'messages_details'  => [
                'from_id'   => $item->from_id,
                'body'  => $item->body,
                'attachment'    => $message?->attachment?: null,
                'created_at'    => $item->max_created_at,
                'seen'  => $item->seen,
                'unseen_count'  => $item->unseen_count,
            ],
        ];
    }

    public function userMap($item): array
    {
        return [
            'id'    => $item->id,
            'name'  => $item->name,
            'username'  => $item->username,
            'email' => $item->email,
            'phone' => $item->phone,
            'privacy' => $item->privacy,
            'active-status' => $item->active_status,
            'avatar'    => $item->avatar? url('storage/avatars/'.basename($item->avatar)): null
        ];
    }

    public function storyMap(Story $story): array
    {
        $data = $story->only([
            'id', 'user_id', 'content', 'content_background',
            'privacy','created_at']);

        $data['media'] = [];
        if($story->relationLoaded('media') && $story->media){
            $data['media'] = $story->media->map([$this, 'mediaMap']);
        }

        $data['views'] = [];
        if($story->relationLoaded('views') && $story->views){
            $data['views'] = $story->views->map(function($friend)use($data){

                $friend->avatar = $friend->avatar? url('storage/avatars/'.basename($friend->avatar)): null;
                $friend_ = $friend->only([
                    'id', 'name', 'username', 'email', 'phone', 'privacy',
                    'avatar'
                ]);
                $friend_['view_at'] = $friend->pivot->created_at;
                return $friend_;
            });
        }

        if($story->user_id == auth()->id()){
            $data['viewed_at'] = $story->created_at;
        }else{
            $view = $data['views']->filter(function($item){
                return $item['id'] == auth()->id();
            })->first();
            $data['viewed_at'] = null;
            if($view){
                $data['viewed_at'] = $view['view_at'];
            }
        }

        $data['author'] = [];
        if($story->relationLoaded('user')){
            $data['author'] = [
                'id'    => $story->user->id,
                'name'  => $story->user->name,
                'username'  => $story->user->username,
                'email' => $story->user->email,
                'phone' => $story->user->phone,
                'privacy' => $story->user->privacy,
                'avatar'    => $story->user->avatar? url('storage/avatars/'.basename($story->user->avatar)): null
            ];
        }

        $data['reaction_summary'] = $story->getReactionsSummary();

        $data['my_reactions'] = [];
        if($story->relationLoaded('myReactions')){
            $data['my_reactions'] = $story->myReactions->map(function($reaction){
                return [
                    'reaction'  => $reaction->name,
                    'created_at'    => $reaction->created_at,
                ];
            });
        }

        return $data;
    }

    public function commentMap(Comment $comment): array
    {
        $data = $comment->only(['id', 'comment', 'created_at']);
        $data['reaction_summary'] = $comment->getReactionsSummary();
        $data['commenter'] = $comment->commenter->only(['id', 'name', 'username', 'email', 'avatar']);
        $data['commenter']['avatar'] = url('storage/avatars/'.basename($data['commenter']['avatar']));
        $data['media'] = [];
        if($comment->relationLoaded('children')){
            $data['children'] = $comment->children->transform(function($reply){
                $data = $reply->only(['id', 'comment', 'created_at']);
                $data['commenter'] = $reply->commenter->only(['id', 'name', 'username', 'email', 'avatar']);
                $data['commenter']['avatar'] = url('storage/avatars/'.basename($data['commenter']['avatar']));
                $data['reaction_summary'] = $reply->getReactionsSummary();
                if($reply->relationLoaded('media')){
                    $data['media'] = $reply->media->map([$this, 'mediaMap']);
                }

                $data['my_reactions'] = [];
                if($reply->relationLoaded('myReactions')){
                    $data['my_reactions'] = $reply->myReactions->map(function($reaction){
                        return [
                            'reaction'  => $reaction->name,
                            'created_at'    => $reaction->created_at,
                        ];
                    });
                }


                return $data;
            })->sortByDesc('created_at')->values();
        }

        if($comment->relationLoaded('media')){
            $data['media'] = $comment->media->map([$this, 'mediaMap']);
        }

        $data['my_reactions'] = [];
        if($comment->relationLoaded('myReactions')){
            $data['my_reactions'] = $comment->myReactions->map(function($reaction){
                return [
                    'reaction'  => $reaction->name,
                    'created_at'    => $reaction->created_at,
                ];
            });
        }

        return $data;
    }

    public function bookMap(Book $book): array
    {
        $data = [
            'title' => $book->title,
            'description' => $book->description,
            'book_id'   => $book->id,
            'parent_id' => $book->parent_id,
            'privacy' => $book->privacy,
            'user_id'   => $book->user_id,
            'created_at'    => $book->created_at,
            'category'  => null,
        ];

        if($book->relationLoaded('mediaFiles')){
            $data['media_files'] = $book->mediaFiles->map([$this, 'mediaMap']);
        }

        if($book->relationLoaded('children')){
            $data['children'] = $book->children;
        }

        if($book->relationLoaded('category') && $book->category) {
            $data['category'] = [
                'id' => $book->category->id,
                'title' => $book->category->title,
            ];
        }

        return $data;
    }

    public function mediaMap(\Spatie\MediaLibrary\MediaCollections\Models\Media $media): array
    {
        return [
            'name' => $media->name,
            'file_name' => $media->file_name,
            'uuid' => $media->uuid,
            'preview_url' => $media->preview_url,
            'original_url' => $media->original_url,
            'order' => $media->order_column,
            'custom_properties' => $media->custom_properties,
            'extension' => $media->extension,
            'size' => $media->size,
            'type' => $media->collection_name,
            'created_at'    => $media->created_at,
            'collection_name'    => $media->collection_name,
            'video_thumbnail' => $media->custom_properties['video_thumbnail'],
        ];
    }

}
