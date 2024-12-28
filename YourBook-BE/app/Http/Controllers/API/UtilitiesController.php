<?php

namespace App\Http\Controllers\API;

use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UtilitiesController
{
    use ApiResponse;

    /**
     * @return JsonResponse
     */
    public function getCountries(): JsonResponse
    {
        return $this->success("Success",
            DB::table('countries')->get(),
            ResponseAlias::HTTP_OK);
    }


    /**
     * @return JsonResponse
     */
    public function getInterests(Request $request): JsonResponse
    {
        $lang = $request->header('Accept-Language') == 'en' ? 'en': 'ar';
        
        $interests = DB::table('interests')
            ->where(function($query)use($request){
                if($request->search){
                    return $query->where('title', 'LIKE', '%'.$request->search.'%')
                        ->orWhere('title_ar', 'LIKE', '%'.$request->search.'%');
                }
            })->get()->map([$this, 'interestMap']);
        
        return $this->success("Success",
            $interests,
            ResponseAlias::HTTP_OK);
    }
    
    
    public function storeInterests(Request $request): JsonResponse
    {
        $validator = Validator::make($request->toArray(), [
            'title'         => 'required|string',
            'title_ar'      => 'required|string',
        ]);

        if($validator->fails()){
            return $this->error($validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        
        $id = DB::table('interests')->insertGetId([
            'title' => $request->title,
            'title_ar'  => $request->title_ar,
            'users_count' => 0,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
        
        $interest = DB::table('interests')->where('id', $id)->get()->map([$this, 'interestMap']);
        
        return $this->success("Success",
            $interest,
            ResponseAlias::HTTP_OK);
        
    }
    
    
    public function interestMap($row){
        $row->title = request()->header('Accept-Language') == 'en' ? $row->title: $row->title_ar;
        unset($row->title_ar);
        return $row;
    }
}
