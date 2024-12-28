<?php

namespace App\Services\AuthService;

use App\Services\AuthServiceInterface;
use Illuminate\Support\Facades\DB;

class AuthService implements AuthServiceInterface
{

    /** Update Current Authenticated user account info */
    public function updateGeneralInfo(array $data): void
    {
        $currentUser = auth()->user();

        if($currentUser){

            if(array_key_exists('user_country_dial_code', $data) && $data['user_country_dial_code']){
                $country = DB::table('countries')->where('dial_code', $data['user_country_dial_code'])->first();
                $data['country_id'] = $country->id;
            }
            $query = [];
            if(array_key_exists('phone', $data) && array_key_exists('dial_code', $data)){
                $query['phone'] = $data['dial_code'].$data['phone'];
            }

            if(array_key_exists('birth_date', $data)){
                $query['birth_date'] = date_create($data['birth_date']);
            }

            if(array_key_exists('country_id', $data)){
                $query['country_id'] = $data['country_id'];
            }

            if(array_key_exists('gender', $data)){
                $query['gender'] = $data['gender'];
            }

            if(array_key_exists('photo', $data) && $data['photo']){
                $avatar = $data['photo']->store('public/avatars');
                $query['avatar'] = $avatar;
            }

            if(array_key_exists('name', $data)){
                $query['name'] = $data['name'];
            }

            $currentUser->update($query);
        }
    }
}
