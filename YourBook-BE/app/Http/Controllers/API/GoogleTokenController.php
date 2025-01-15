<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Google\Client as Google_Client;


class GoogleTokenController extends Controller
{
    use ApiResponse;
    // Verify the Google token and authenticate the user
    public function verifyGoogleToken(Request $request)
{
    $idToken = $request->input('id_token');

    if (!$idToken) {
        return $this->error('ID Token is missing', null, self::$responseCode::HTTP_BAD_REQUEST);
    }

    try {
        // Verify token using Google's API
        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken,
        ]);

        if ($response->failed()) {
            return $this->error('Invalid ID Token', null, self::$responseCode::HTTP_UNAUTHORIZED);

        }

        $payload = $response->json();

        // Validate claims
        if ($payload['iss'] !== 'https://accounts.google.com') {
            return $this->error('Invalid issuer', null, self::$responseCode::HTTP_UNAUTHORIZED);
            
        }

        if ($payload['aud'] !== env('GOOGLE_CLIENT_ID')) {
            return $this->error('Invalid audience', null, self::$responseCode::HTTP_UNAUTHORIZED);

        }

        if ($payload['exp'] < time()) {
            return $this->error('Token expired', null, self::$responseCode::HTTP_UNAUTHORIZED);
            
        }

        // User login or registration logic
        $user = User::firstOrCreate(
            ['email' => $payload['email']],
            [
                'name' => $payload['name'] ?? 'Google User',
                'email_verified_at' => now(),
                'google_id' => $payload['sub'],
            ]
        );

        // Generate access token for authenticated user
        $tokenResult = $user->createToken('GoogleLoginToken');

        return $this->success('Login done successfully', [
            'access_token' => $tokenResult->accessToken,
            'user' => $user
        ], self::$responseCode::HTTP_OK);
        
    } catch (\Exception $e) {
        return $this->error($e->getMessage(), null, self::$responseCode::HTTP_INTERNAL_SERVER_ERROR);
    }
}


}
