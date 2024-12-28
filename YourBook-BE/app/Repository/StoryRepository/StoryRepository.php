<?php

namespace App\Repository\StoryRepository;

use App\Models\Posts\Post;
use App\Models\Stories\Story;
use App\Repository\StoryRepositoryInterface;
use App\Traits\Mapping;

class StoryRepository implements StoryRepositoryInterface
{
    use Mapping;

    public function getStory($story_id): mixed
    {
        return $this->getStories(story_id: $story_id)->first();
    }

    public function getStories($user_id = null, $story_id = null): mixed
    {
        $story = Story::with([
            'media', 'user', 'views',
        ])->where([
            'user_id'   => $user_id ?? auth()->id(),
        ])->when($story_id, fn($query) => $query->where('id', $story_id));

        if($story_id){
            return $story->get()->map([$this, 'storyMap']);
        }

        $story = $story->paginate(20);
        $story->getCollection()->transform([$this, 'storyMap']);

        return $story;
    }

}
