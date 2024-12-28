<?php

namespace App\Jobs;

use App\Models\Auth\User;
use App\Models\Book;
use App\Models\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Vormkracht10\UploadcareTransformations\UploadcareTransformation;

class ProcessImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Media|null $media;
    public array $books;

    /**
     * Create a new job instance.
     */
    public function __construct(Media|null $media, array $books = [])
    {
        $this->media = $media;
        $this->books = $books;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $user = User::find($this->media->model_id);

        if($this->media && $user){
            $configuration = \Uploadcare\Configuration::create(env('UPLOADCARE_PUBLIC_KEY'), env('UPLOADCARE_SECRET_KEY'));
            $api = new \Uploadcare\Api($configuration);

            $filePath = $this->media->getPath();
            $mediaName = $this->media->name;
            $fileName = $this->media->file_name;
            $mimeType = $this->media->mime_type;
            $fileInfo = $api->uploader()->fromPath($filePath, $mimeType, $fileName);
            Log::info("File Uploaded.");

            $fileUuid = $fileInfo->getUuid();
            Log::info("Get UUID");

            $transformation = (new UploadcareTransformation($fileUuid));
            $url = $transformation->quality(quality: 'lighter')->progressive(true);
            Log::info("URL: ".$url);

            $newMedia = $user->addMediaFromUrl($url)->toMediaCollection('images');
            Log::info("New Media Created.");

            $mediaLibUuid = $this->media->uuid;

            // check if not exists .. deleted after uploading.

            if($user->media()->where('uuid', $mediaLibUuid)->exists()){
                

                $newMedia->uuid = $mediaLibUuid;
                $newMedia->name = $mediaName;
                $newMedia->save();
                Log::info('New Media updated UUID');

                foreach($this->books as $book){
                    Log::info('Attaching media id:'.$newMedia->id.' to book id: '.$book->id);
                    $book->media()->syncWithoutDetaching([$newMedia->id]);
                }
                
                $this->media->delete();
                Log::info("Old Media Deleted.");
            }else{
                $newMedia->delete();
            }

            $api->file()->deleteFile($fileInfo);
            Log::info('file Deleted From API');


        }
    }
}
