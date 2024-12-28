<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadHandlerController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function handler(Request $request): JsonResponse
    {
        //config(['media-library.disk_name' => 'public']);
        // get file URL to media library...
        $user = auth()->user();
        $uploadedMedia = [];
        foreach($request->media ?? [] as $mediaItem){
             $newMedia = $user->addMediaFromUrl($mediaItem['cdnUrl'])
                ->withCustomProperties([
                    'uc_uuid'   => $mediaItem['uuid'],
                    'uc_cdnUrl' => $mediaItem['cdnUrl'],
                ])
                 ->usingName($mediaItem['name'])
                 ->toMediaCollection($mediaItem['isImage'] != 'false' ? 'images': 'videos');
            $mediaExtension = explode('/', $newMedia->mime_type);
            $newMedia->file_name = "{$newMedia->uuid}.{$mediaExtension[1]}";
            $newMedia->save();


//            if($mediaItem['isImage'] == 'false'){
//                $thumb_url = $newMedia->getUrl('thumb');
//            }
            $thumb_url = 'https://placehold.co/600x400?text=Video+File';

            $uploadedMedia[] = collect([$newMedia])->map(function($media)use($mediaItem, $thumb_url){
                return [
                    'media_id'  => $media->id,
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'uuid' => $media->uuid,
                    'preview_url' => $media->preview_url,
                    'original_url' => $media->original_url,
                    'order' => $media->order_column,
                    'custom_properties' => $media->custom_properties,
                    'extension' => $media->extension,
                    'size' => $media->size,
                    'isImage'   => !($mediaItem['isImage'] == 'false'),
                    'thumb_url'     => $thumb_url,
                    'description'   => $mediaItem['description'],
                ];
            })->first();
        }

        foreach($request->toDelete ?? [] as $item){
            $user->media()->find($item['media_id'])->delete();
        }

        return response()->json($uploadedMedia, 201);
    }

}
