<?php

namespace App\Http\Controllers\API;

use App\Jobs\ProcessImageJob;
use App\Traits\ApiResponse;
use Filament\Forms\Components\FileUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Vormkracht10\UploadcareTransformations\UploadcareTransformation;

class MediaController
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $uuid = request()->uuid;
        $collection = \request()->collection;
        $per_page = \request()->per_page;

        $data = auth()->user()->media()->where(function($query)use($uuid, $collection){
            if($uuid){
                $query->where('media.uuid', $uuid);
            }
            if($collection){
                $query->where('media.collection_name', $collection);
            }
        })->paginate($per_page ?? 15);


        $responseData = $data->getCollection()->map([$this, 'mediaMap'])->keyBy('uuid')->toArray();
        $responseData = array_values($responseData);

        $paginatedTransformedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $responseData,
            $data->total(),
            $data->perPage(),
            $data->currentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        if($uuid){
            if(!count($responseData)){
                return $this->error("No media found with this UUID", [
                    'uuid'   => ['No media found with this UUID']
                ], self::$responseCode::HTTP_NOT_FOUND);
            }
        }

        return $this->success("Success", $paginatedTransformedData, self::$responseCode::HTTP_OK);
    }

    public function upload(Request $request): JsonResponse|string
    {
        // Validation
        $validator = Validator::make($request->toArray(), [
            'image'     => 'required_without_all:audio,video|image|max:'.env('MAX_UPLOAD_FILE_SIZE'),
            'audio'     => 'required_without_all:image,video|audio|duration_max:'.env('MAX_AUDIO_FILE_DURATION').'|max:'.env('MAX_UPLOAD_FILE_SIZE'),
            // 'video'     => 'required_without_all:image,audio|video|duration_max:'.env('MAX_VIDEO_FILE_DURATION').'|max:'.env('MAX_UPLOAD_FILE_SIZE'),
            'video'     => 'required_without_all:image,audio',
            'book_id'   => 'nullable|exists:books,id',
        ]);

        if($validator->fails()){
            return $this->error($validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $book = null;
        if($request->book_id){
            $book = Book::where('user_id', auth()->id())->find($request->book_id);
            if(!$book){
                return $this->error("You do not have access to this book.", [
                    'book_id'   => ['You do not have access to this book.']
                ], self::$responseCode::HTTP_UNAUTHORIZED);
            }
        }


        $input_type = array_keys($validator->getData());
        $input_type = $input_type[0] == 'book_id' ? $input_type[1]: $input_type[0];

        return match ($input_type) {
            'image' => $this->imageHandler($request, $book),
            'video' => $this->videoHandler($request, $book),
            'audio' => $this->audioHandler($request),
            default => response()->json(),
        };
    }

    /**
     * @param $request
     * @param null $book
     * @return JsonResponse
     */
    private function imageHandler($request, $book = null): JsonResponse
    {
        // upload to server storage ...
        // config(['media-library.disk_name' => 'public']);
        $media = auth()->user()->addMedia($request->file('image'))->toMediaCollection('images');

        if($book){
            $book->mediaFiles()->syncWithoutDetaching([$media->id]);
        }

        // dispatch new job to compress image and re-store it again ...
        // dispatch(new ProcessImageJob($media, [$book]));

        $media = collect([$media])->map([$this, 'mediaMap'])->keyBy('uuid')->toArray();
        $media = array_values($media);
        return $this->success('Image Uploaded.', $media, self::$responseCode::HTTP_CREATED);
    }

    // private function videoHandler($request, $book = null): JsonResponse
    // {
    //     $media = auth()->user()->addMedia($request->file('video'))->toMediaCollection('videos');

    //     if($book){
    //         $book->mediaFiles()->syncWithoutDetaching([$media->id]);
    //     }

    //     $media = collect([$media])->map([$this, 'mediaMap'])->keyBy('uuid')->toArray();
    //     $media = array_values($media);
    //     return $this->success("Video Uploaded.", $media, self::$responseCode::HTTP_CREATED);
    // }

    //updated
    private function videoHandler($request, $book = null): JsonResponse
{
    if (!$request->hasFile('video')) {
        Log::error("No video file found in the request.");
        return response()->json(['error' => 'No video uploaded.'], 400);
    }
    $media = auth()->user()->addMedia($request->file('video'))
        ->storingConversionsOnDisk('public') // Ensure it's stored in 'public'
        ->toMediaCollection('videos', 'public'); // Force storing in 'public'
    Log::error("Actual Stored Path: " . $media->getPath());
    
    
    
    if ($book) {
        $book->mediaFiles()->syncWithoutDetaching([$media->id]);
    }
    $videoPath = $media->getPath();
Log::error("Corrected Video Path: " . $videoPath);


    // Generate video thumbnail
    $thumbnailPath = $this->generateVideoThumbnail($media->getPath(), $media->id);

    // Store the thumbnail URL in media's custom properties
    $media->setCustomProperty('video_thumbnail', $thumbnailPath);
    $media->save();

    $media = collect([$media])->map([$this, 'mediaMap'])->keyBy('uuid')->toArray();
    $media = array_values($media);
    
    return $this->success("Video Uploaded.", $media, self::$responseCode::HTTP_CREATED);
}

private function generateVideoThumbnail($videoPath, $mediaId)
{
    // Ensure the path is absolute
    $fullVideoPath = $videoPath; // Directly use the absolute path

    Log::error("Full Video Path: " . $fullVideoPath);

    $thumbnailName = "thumbnails/video_$mediaId.jpg";
    $thumbnailPath = storage_path("app/public/$thumbnailName");

    // Ensure storage directory exists
    if (!file_exists(dirname($thumbnailPath))) {
        mkdir(dirname($thumbnailPath), 0755, true);
    }

    // Run the command and log output
    $command = "ffmpeg -i \"$fullVideoPath\" -ss 00:00:01 -vframes 1 \"$thumbnailPath\" 2>&1";
    Log::error("Running Command: " . $command);
    $output = shell_exec($command);

    Log::error("FFmpeg Output: " . $output);

    return asset("storage/$thumbnailName");
}



    private function audioHandler($request): JsonResponse
    {
        $media = auth()->user()->addMedia($request->file('audio'))->toMediaCollection('audios');
        $media = collect([$media])->map([$this, 'mediaMap'])->keyBy('uuid')->toArray();
        $media = array_values($media);
        return $this->success("Audio Uploaded.", $media, self::$responseCode::HTTP_CREATED);
    }


    /**
     * @param $media_uuid
     * @return JsonResponse
     */
    public function destroy($media_uuid): JsonResponse
    {
        // Validation
        $validator = Validator::make(['uuid' => $media_uuid], [
            'uuid'   => 'required|exists:media,uuid'
        ]);

        if($validator->fails()){
            return $this->error($validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $media = auth()->user()->media()->where('uuid', $media_uuid)->first();
        if(!$media){
            return $this->error("This Media do not belongs to this user.",
                ['category_id'  => 'This Media do not belongs to this user.'],
                ResponseAlias::HTTP_UNAUTHORIZED);
        }

        $media->delete();

        return $this->success('Deleted.', null, self::$responseCode::HTTP_OK);
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
            'video_thumbnail' => $media->custom_properties['video_thumbnail'] ?? null,
        ];
}
}