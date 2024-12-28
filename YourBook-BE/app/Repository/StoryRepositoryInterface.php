<?php

namespace App\Repository;

interface StoryRepositoryInterface
{

    /**
     * @param $story_id
     * @return mixed
     */
    public function getStory($story_id): mixed;


    /**
     * @param $user_id
     * @param $story_id
     * @return mixed
     */
    public function getStories($user_id = null, $story_id = null): mixed;

}
