<?php

namespace App\Repository\PostRepository;

use App\Enum\PrivacyEnum;
use App\Models\Auth\User;
use App\Models\Posts\Post;
use App\Repository\PostRepositoryInterface;
use App\Traits\Mapping;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository implements PostRepositoryInterface
{
    use Mapping;
    /**
     * @param $post_id
     * @return mixed
     *
     */
    public function getPost($post_id): mixed
    {
        return $this->getPosts(post_id: $post_id)->first();
    }


    public function getPosts($user_id = null, $post_id = null): mixed
    {
        $post = Post::with([
                'mood', 'taggedFriends', 'taggedBooks', 'media', 'user', 'tagsTranslated'
            ])->withCount(['comments'])->when(auth()->user(), function($query){
                $query->with('myReactions');
            })->where([
                'user_id'   => $user_id ?? auth()->id(),
            ])->when($post_id, fn($query) => $query->where('id', $post_id))
            ->orderByDesc('posts.created_at');

        if($post_id){
            return $post->get()->map([$this, 'postMap']);
        }

        $post = $post->paginate(20);
        $post->getCollection()->transform([$this, 'postMap']);

        return $post;
    }

    public function favorites(): mixed
    {
        $posts = auth()->user()->favorites(Post::class)->with([
                'mood', 'taggedFriends', 'taggedBooks', 'media', 'user', 'tagsTranslated'
            ])->withCount(['comments'])->when(auth()->user(), function($query){
                $query->with('myReactions');
            })->orderByDesc('posts.created_at');

        $posts = $posts->paginate(20);
        $posts->getCollection()->transform([$this, 'postMap']);

        return $posts;
    }


    /**
     * @param array $hash_tags
     * @return mixed
     */
    public function getPostsByHashtags(array $hash_tags = []): mixed
    {
        $currentAuthUserFriends = $this->getFriendsID();

        $post = Post::with([
                'mood', 'taggedFriends', 'taggedBooks', 'media', 'user', 'tagsTranslated'
            ])->withCount(['comments'])
            ->withAnyTags($hash_tags)
            ->where(function($query)use($currentAuthUserFriends){
                $query->whereIn('user_id', $currentAuthUserFriends)
                    ->orWhere('privacy', 'public');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $post->getCollection()->transform([$this, 'postMap']);
        return $post;
    }

    public function searchContent($text): mixed
    {
        $currentAuthUserFriends = $this->getFriendsID();

        $post = Post::with([
                'mood', 'taggedFriends', 'taggedBooks', 'media', 'user', 'tagsTranslated'
            ])->withCount(['comments'])
            ->where(function($query)use($text){
                $query->where('content', 'LIKE', '%'.$text.'%')
                    ->where('privacy', PrivacyEnum::PUBLIC);
            })
            ->orWhere(function($query)use($currentAuthUserFriends, $text){
                $query->where('content', 'LIKE', '%'.$text.'%')
                    ->whereIn('user_id', $currentAuthUserFriends);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $post->getCollection()->transform([$this, 'postMap']);
        return $post;
    }

    public function getPostsFeed(): mixed
    {
        $per_page = 10;
//        $counts = [
//            'friends'   => round(0.6*$per_page),
//            'country'   => round(0.4*$per_page),
//            'interests' => round(0.0*$per_page)
//        ];

        $currentAuthUserFriends = $this->getFriendsID();
        $peopleSameCountry = User::where('country_id', auth()->user()->country_id ?? 999)->select('users.id')
            ->limit(300)->orderBy('updated_at', 'desc')->get()->pluck(['id']);

        $posts = Post::with([
                'mood', 'taggedFriends', 'taggedBooks', 'media', 'user', 'tagsTranslated', 'myReactions'
            ])->withCount(['comments'])
            ->where(function($query)use($currentAuthUserFriends){
                $query->whereIn('user_id', $currentAuthUserFriends)
                    ->Where('privacy', '!=', PrivacyEnum::PRIVATE);
            })
            ->orWhere(function($query)use($peopleSameCountry){
                $query->whereIn('user_id', $peopleSameCountry)
                    ->where('privacy', PrivacyEnum::PUBLIC);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($per_page);
        $posts->getCollection()->transform([$this, 'postMap']);

       return $posts;
    }

    public function profilePosts($user_id): mixed
    {
        $user = User::find($user_id);
        $areFriends = $user->isFriendWith(auth()->user());

        $post = Post::with([
            'mood', 'taggedFriends', 'taggedBooks', 'media', 'user', 'tagsTranslated', 'myReactions'
        ])->withCount(['comments'])->when(auth()->user(), function($query){
            $query->with('myReactions');
        })->where([
            'user_id'   => $user_id ?? auth()->id(),
        ])->where(function ($query)use($areFriends){
            if($areFriends){
                $query->where('privacy', '!=', PrivacyEnum::PRIVATE);
            }else{
                $query->where('privacy', PrivacyEnum::PUBLIC);
            }
        })
        ->orderByDesc('posts.created_at');

        $post = $post->paginate(20);
        $post->getCollection()->transform([$this, 'postMap']);

        return $post;
    }

    private function getFriendsID(): mixed
    {
        return auth()->user()->getFriendsQueryBuilder()->select('users.id')->get()->pluck(['id']);
    }

    public function postMap(Post $post): array
    {
        $data = $post->only(['id', 'user_id', 'content', 'content_background', 'location',
            'share_link', 'privacy', 'show_in_feed', 'comments_count', 'created_at']);

        $data['hash_tags'] = [];
        if($post->relationLoaded('tagsTranslated')){
            $data['hash_tags'] = $post->tagsTranslated->map([$this, 'tagMap']);
        }

        $data['mood'] = null;
        if($post->relationLoaded('mood') && $post->mood){
            $data['mood'] = $post->mood->only([
                'id', 'activity', 'activity_pic', 'mood', 'mood_pic'
            ]);
        }

        $data['tagged_friends'] = [];
        if($post->relationLoaded('taggedFriends') && $post->taggedFriends){
            $data['tagged_friends'] = $post->taggedFriends->map(function($friend){
                $friend->avatar = url('storage/avatars/'.basename($friend->avatar));
                return $friend->only([
                    'id', 'name', 'username', 'email', 'phone', 'privacy',
                    'avatar'
                ]);
            });
        }
        $data['tagged_books'] = [];
        if($post->relationLoaded('taggedBooks') && $post->taggedBooks){
            $data['tagged_books'] = $post->taggedBooks->map([$this, 'bookMap']);
        }

        $data['media'] = [];
        if($post->relationLoaded('media') && $post->media){
            $data['media'] = $post->media->map([$this, 'mediaMap']);
        }

        $data['author'] = [];
        if($post->relationLoaded('user')){
            $data['author'] = [
                'id'    => $post->user->id,
                'name'  => $post->user->name,
                'username'  => $post->user->username,
                'email' => $post->user->email,
                'phone' => $post->user->phone,
                'privacy' => $post->user->privacy,
                'avatar'    => $post->user->avatar? url('storage/avatars/'.basename($post->user->avatar)): null
            ];
        }

        if(auth()->id() == $post->user->id){
            $data['isFriend'] = null;
            $data['isFollowing'] = null;
        }else{
            $author = User::find($post->user->id);
            $data['isFriend'] = auth()->user()->isFriendWith($author);
            $data['isFollowing'] = auth()->user()->isFollowing($author);
        }

        $data['is_favorite'] = $post->isFavoritedBy(auth()->user());

        $data['my_reactions'] = [];
        if($post->relationLoaded('myReactions')){
            $data['my_reactions'] = $post->myReactions->map(function($reaction){
                return [
                    'reaction'  => $reaction->name,
                    'created_at'    => $reaction->created_at,
                ];
            });
        }

        $data['reaction_summary'] = $post->getReactionsSummary();

        $data['sharedPost'] = null;
        if($post->relationLoaded('sharedPost') && $post->getRelation('sharedPost')){

            $data['sharedPost'] = $this->postMap(
                $post->getRelation('sharedPost')
                    ->load([
                        'mood', 'taggedFriends', 'taggedBooks', 'media', 'user', 'tagsTranslated'
                    ])->loadCount(['comments'])
            );
        }

        return $data;
    }

}
