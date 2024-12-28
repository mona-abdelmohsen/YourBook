<?php

namespace App\Services\Dropzone;

use App\Services\DropzoneInterface;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class DropzoneService implements DropzoneInterface
{
    public string $temp_path = 'temp';

    /**
     * @param $temp_file_name
     * @param $newLocation
     */
    public function moveFromTemp($temp_file_name, $newLocation): void{
        $path = storage_path($this->temp_path.'/'.$temp_file_name);
        if(file_exists($path)){
            rename($path, storage_path('app/'.$newLocation));
        }
    }

    public function store(array|UploadedFile|null $file): array
    {
        $path = storage_path($this->temp_path);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $name = uniqid() . '_' . trim($file->getClientOriginalName());
        $file->move($path, $name);

        return [
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ];
    }
}
