<?php

namespace App\Repository;

interface PostRepositoryInterface
{
    /**
     * @param $post_id
     * @return mixed
     */
    public function getPost($post_id): mixed;

    /**
     * @param array $hash_tags
     * @return mixed
     */
    public function getPostsByHashtags(array $hash_tags = []): mixed;

    /**
     * @param $user_id
     * @return mixed
     */
    public function profilePosts($user_id): mixed;
}
