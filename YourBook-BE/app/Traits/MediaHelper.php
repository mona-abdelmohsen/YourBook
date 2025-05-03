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
    // public function mediaHandler(HasMedia $model, $target_collection): mixed
    // {
    //     if (!request()->has($target_collection) || !request()->{$target_collection}) {
    //         return null;
    //     }
        
        
    //     // upload to server storage ...
    //     //config(['media-library.disk_name' => 'public']);

    //     // dispatch new job to compress image and re-store it again ...


    //     return $model->addMultipleMediaFromRequest([$target_collection])
    //         ->each(function ($fileAdder) use ($target_collection) {
    //             $fileAdder->toMediaCollection($target_collection);
    //         });
    
    // }

    public function mediaHandler(HasMedia $model, $target_collection): mixed
    {
        if (!request()->has($target_collection) || !request()->{$target_collection}) {
            return null;
        }
    
        try {
            // Try to add the media file to the collection
            return $model->addMultipleMediaFromRequest([$target_collection])
                ->each(function ($fileAdder) use ($target_collection) {
                    $fileAdder->toMediaCollection($target_collection);
                });
        } catch (\Exception $e) {
            // Catch any exceptions related to media upload and return a custom error message
            return $this->error('The file upload failed. Please check the file type and size.', [], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    
}
