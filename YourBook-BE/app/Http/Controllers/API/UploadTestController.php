<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Tag\ImageTag;
use Cloudinary\Transformation\Delivery;
use Cloudinary\Transformation\Format;
use Cloudinary\Transformation\Quality;
use Cloudinary\Transformation\Resize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadTestController extends Controller
{
    public function __construct()
    {
        Configuration::instance();
    }

    public function upload(Request $request){

        $file = $request->file('image');

        /** Upload to server */
        $fileUrl = $file->store('public/temp');
        $serverUrl = storage_path('app/'.$fileUrl);

        /** Upload from server to Cloud */
//        $fp = @fopen($serverUrl, "rb");
//        $upload = (new UploadApi())->upload($fp);

        $cld = new Cloudinary([
            'cloud' => [
                'cloud_name' => 'dgypou5nk',
                'api_key' => '171239837543394',
                'api_secret' => 'TkASUcGXzo8VGFvUQJ0IoG3EI6Q'],
            'url' => [
                'secure' => true]]);
        $cld->imageTag('wdrxag4wt35a11qnsq2a')
            ->resize(Resize::scale()->width(200))
            ->delivery(Delivery::quality(Quality::auto()))
            ->delivery(Delivery::format(Format::auto()));

//        "url": "http://res.cloudinary.com/{dgypou5nk}/{image}/upload/v{1702576331}/{wdrxag4wt35a11qnsq2a}.{png}",
        /** Remove the file from Server */
        if(Storage::exists($fileUrl)){
            Storage::delete($fileUrl);
        }

        return response()->json([
            'url'   => [
//                'public '   => $publicUrl,
                'server'     => $serverUrl,
                'original'  => $fileUrl,
            ],
            'file'  => $cld,
        ], 201);
    }

}
