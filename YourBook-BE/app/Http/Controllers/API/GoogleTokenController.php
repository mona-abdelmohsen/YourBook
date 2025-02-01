<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Lang;
use Google\Client as Google_Client;


class GoogleTokenController extends Controller
{
    use ApiResponse;
    // Verify the Google token and authenticate the user

    public function verifyGoogleToken(Request $request)
    {
        $idToken = $request->input('id_token');

        if (!$idToken) {
            return $this->error(Lang::has('validation.custom.token_missing') ? Lang::get('validation.custom.token_missing') : 'Token is missing', null, self::$responseCode::HTTP_BAD_REQUEST);
        }

        try {
            // Load Google service credentials
            $googleService = json_decode(file_get_contents(config_path('google-service.json')), true);

            // Verify token using Google's API
            $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $idToken,
            ]);

            if ($response->failed()) {
                return $this->error(Lang::has('validation.custom.invalid_token') ? Lang::get('validation.custom.invalid_token') : 'Invalid token', null, self::$responseCode::HTTP_UNAUTHORIZED);
            }

            $payload = $response->json();

            if ($payload['iss'] !== 'https://accounts.google.com') {
                return $this->error(Lang::has('validation.custom.invalid_issuer') ? Lang::get('validation.custom.invalid_issuer') : 'Invalid token issuer', null, self::$responseCode::HTTP_UNAUTHORIZED);
            }

            // Extract client_id from the token response
            $clientIdFromToken = $payload['aud'];

            // Find the matching platform
            $validClientIds = [
                $googleService['web']['client_id'],
                $googleService['android']['oauth_client_id'],
                $googleService['ios']['oauth_client_id']
            ];

            if (!in_array($clientIdFromToken, $validClientIds)) {
                return $this->error(Lang::has('validation.custom.invalid_audience') ? Lang::get('validation.custom.invalid_audience') : 'Invalid token audience', null, self::$responseCode::HTTP_UNAUTHORIZED);
            }

            if ($payload['exp'] < time()) {
                return $this->error(Lang::has('validation.custom.token_expired') ? Lang::get('validation.custom.token_expired') : 'Token has expired', null, self::$responseCode::HTTP_UNAUTHORIZED);
            }

            // User login or registration logic
            $user = User::firstOrCreate(
                ['email' => $payload['email']],
                [
                    'name' => $payload['name'] ?? (Lang::has('validation.custom.default_user_name') ? Lang::get('validation.custom.default_user_name') : 'User'),
                    'email_verified_at' => now(),
                    'google_id' => $payload['sub'],
                ]
            );

            $tokenResult = $user->createToken('GoogleLoginToken');

            return $this->success(Lang::has('validation.custom.success') ? Lang::get('validation.custom.success') : 'Login successful', [
                'access_token' => $tokenResult->accessToken,
                'user' => $user
            ], self::$responseCode::HTTP_OK);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, self::$responseCode::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


}
