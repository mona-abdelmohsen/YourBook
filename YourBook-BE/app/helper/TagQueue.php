<?php

namespace App\helper;

class TagQueue
{
    private array $tags = [];

    public function addTag($tag): void
    {
        $this->tags[] = $tag;
    }

    public function getTags(): array
    {
        return $this->tags;
    }
}
