<?php

namespace App\Models\Auth;
use Illuminate\Database\Eloquent\Model;

class OauthRefreshTokens extends Model
{
    protected $table = 'oauth_refresh_tokens';
    public $timestamps = false;
}


