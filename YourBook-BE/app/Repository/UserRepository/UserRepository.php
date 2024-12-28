<?php

namespace App\Repository\UserRepository;

use App\Models\Auth\User;
use App\Repository\UserRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{

    public function getUserInterests(int $user_id = null): Collection|array|null
    {
        if(!$user_id){
            $user_id = auth()->id();
        }
        return (DB::table('user_interests')->where('user_id', $user_id)
            ->join('interests', 'user_interests.interest_id', 'interests.id')
            ->select(['interests.title', 'interests.id'])
            ->get());
    }

    public function updateUserInterests(array $data)
    {
        $query = [];
        foreach($data as $i){
            $query[] = [
                'user_id' => auth()->id(),
                'interest_id' => $i,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }
        if(count($query)){
            DB::table('user_interests')->where('user_id', auth()->id())->delete();
            DB::table('user_interests')->insert($query);
        }
    }

    public function getTopAccounts($limit = 20): Collection|array|null
    {
        return (User::where('users.id', '!=', auth()->id())
//            ->leftJoin('user_interests', 'users.id', 'user_interests.user_id')
//            ->orWhere(function($query){
//                $query->orWhere('country_id', $this->country_id)
//                ->orWhereIn('user_interests.interest_id', $this->interests_selected);
//            })
//            ->groupBy('users.id')
            ->select([
                'users.id AS user_id', 'users.name', 'country_id', 'phone', 'gender',
                'birth_date', 'avatar', 'email'
            ])
            ->get()->random($limit)->map(function($row){
                if($row->avatar && $row->avatar != 'avatar.png'){
                    $row->avatar = url('storage/avatars/'.basename($row->avatar));
                }else{
                    $row->avatar = 'https://via.placeholder.com/100x100.png';
                }
                $row->isFollowing = auth()->user()->isFollowing($row);
                $row->areFriends = auth()->user()->isFriendWith($row);
                return $row;
            }));
    }

    public function markAccountAsUpdated($user_id = null)
    {
        if(!$user_id){
            auth()->user()->update(['account_updated' => 1]);
        }else{
            $user = User::find($user_id);
            if($user){
                $user->account_updated = 1;
                $user->save();
            }
        }
    }

    private function parsePhoneNumber($dial_code, $phone)
    {
        if (str_starts_with($phone, $dial_code) && $dial_code) {
            $phone = substr($phone, strlen($dial_code));
        }

        return $phone;
    }

    public function getAccountGeneralInfo($user_id = null): Collection|array|null
    {
        if(!$user_id){
            $user_id = auth()->id();
        }

        $userModel = User::find($user_id);

        $user = User::where('users.id', $user_id)
            ->leftJoin('countries', 'countries.id', 'users.country_id')
            ->select([
                'users.id AS user_id', 'users.account_updated', 'fcm_token',
                'users.name', 'country_id', 'phone', 'birth_date', 'gender', 'avatar',
                'countries.name AS country_name', 'countries.dial_code', 'mobile_verified_at', 'email_verified_at',
            ])->first();


        if($user->birth_date){
            $user->birth_date = date_format(date_create($user->birth_date), 'Y-m-d');
        }

        if($user->avatar && $user->avatar != 'avatar.jpg'){
            $user->avatar = url('storage/avatars/'.basename($user->avatar));
        }

        $user->phone = $this->parsePhoneNumber($user->dial_code, $user->phone);

        $user->interests = $this->getUserInterests();

        $user->followers_count = $userModel->followersCount();
        $user->followings_count = $userModel->followings()->count();
        $user->posts_count = $userModel->posts()->count();
        $user->friends_count = $userModel->friends()->count();

        if($user->id != auth()->id()){
            $userModel = User::find($user->user_id);
            $user->isFollowing = auth()->user()->isFollowing($userModel);
            $user->areFriends = auth()->user()->isFriendWith($userModel);
        }

        return collect($user);
    }

}
