<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait ReactionHelper
{
    public function myReactions(): MorphToMany
    {
        $user = auth()->user();
        return $this->morphToMany('DevDojo\\LaravelReactions\\Models\\Reaction', 'reactable')
            ->where('responder_id', $user->id)
            ->where('responder_type', get_class($user))
            ->withPivot(['responder_id', 'responder_type']);
    }


}
