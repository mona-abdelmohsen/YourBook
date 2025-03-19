<?php

namespace App\Http\Controllers\API;

use App\Traits\ApiResponse;
use Illuminate\Http\Response;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ServerRequestInterface;

class CustomAccessTokenController extends AccessTokenController
{
    use ApiResponse;

    /**
     * Create a new controller instance.
     *
     * @param AuthorizationServer $server
     * @param TokenRepository $tokens
     * @return void
     */
    public function __construct(AuthorizationServer $server,
                                TokenRepository $tokens)
    {
        $this->server = $server;
        $this->tokens = $tokens;
    }

    /**
     * Hooks in before the AccessTokenController issues a token
     *
     *
     * @param  ServerRequestInterface $request
     * @return Response|void
     */
//     public function issueUserToken(ServerRequestInterface $request)
//     {
//         $httpRequest = request();

//         if ($httpRequest->grant_type == 'password') {

//             $user = \App\Models\Auth\User::where('email', $httpRequest->username)->first();

// //            if($user && !$user->hasVerifiedEmail()){
// //                return $this->error('Email Not Verified.', ['email' => 'Please Verify your Email Account.'], 401);
// //            }

//             // If the validation is successfull:
//             return $this->issueToken($request);
//         }
//     }

public function issueUserToken(ServerRequestInterface $request)
{
    $httpRequest = request();

    if ($httpRequest->grant_type == 'password') {
        $user = \App\Models\Auth\User::where('email', $httpRequest->username)->first();
//            if($user && !$user->hasVerifiedEmail()){
//                return $this->error('Email Not Verified.', ['email' => 'Please Verify your Email Account.'], 401);
//            }

        // Prevent login for disabled users
        if (!$user->enable) {
            return $this->error('Your account has been disabled. Please contact support.', null, 403);
        }

        // Proceed with issuing token
        return $this->issueToken($request);
    }
}
}
