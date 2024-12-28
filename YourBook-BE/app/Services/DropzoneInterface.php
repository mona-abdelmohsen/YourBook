<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

interface DropzoneInterface
{

    /**
     * @param array|UploadedFile|null $file
     * @return array ['name' => ..., 'original_name' => ...]
     */
    public function store(array|UploadedFile|null $file): array;

    /**
     * @param $temp_file_name
     * @param $newLocation
     * @return void
     */
    public function moveFromTemp($temp_file_name, $newLocation): void;
}
