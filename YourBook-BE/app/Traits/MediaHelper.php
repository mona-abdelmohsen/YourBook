<?php

namespace App\Traits;

use App\Models\Posts\Post;
use Illuminate\Http\JsonResponse;
use Spatie\MediaLibrary\HasMedia;

trait MediaHelper
{

    /**
     * @param HasMedia $model
     * @param $target_collection
     * @return JsonResponse
     */
    public function mediaHandler(HasMedia $model, $target_collection): mixed
    {
        if (!request()->has($target_collection) || !request()->{$target_collection}) {
            return null;
        }
        
        
        // upload to server storage ...
        //config(['media-library.disk_name' => 'public']);

        // dispatch new job to compress image and re-store it again ...


        return $model->addMultipleMediaFromRequest([$target_collection])
            ->each(function ($fileAdder) use ($target_collection) {
                $fileAdder->toMediaCollection($target_collection);
            });
    
    }

}
