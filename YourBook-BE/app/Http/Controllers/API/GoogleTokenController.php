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

    public function verifyGoogleToken(Request $request)
    {
        $idToken = $request->input('id_token');

        if (!$idToken) {
            return $this->error(Lang::has('validation.custom.token_missing') ? Lang::get('validation.custom.token_missing') : 'Token is missing', null, self::$responseCode::HTTP_BAD_REQUEST);
        }

        try {
            $googleService = json_decode(file_get_contents(config_path('google-service.json')), true);
            $response = Http::get('https://oauth2.googleapis.com/tokeninfo', ['id_token' => $idToken]);

            if ($response->failed()) {
                return $this->error(Lang::has('validation.custom.invalid_token') ? Lang::get('validation.custom.invalid_token') : 'Invalid token', null, self::$responseCode::HTTP_UNAUTHORIZED);
            }

            $payload = $response->json();

            if ($payload['iss'] !== 'https://accounts.google.com') {
                return $this->error(Lang::has('validation.custom.invalid_issuer') ? Lang::get('validation.custom.invalid_issuer') : 'Invalid token issuer', null, self::$responseCode::HTTP_UNAUTHORIZED);
            }

            if ($payload['exp'] < time()) {
                return $this->error(Lang::has('validation.custom.token_expired') ? Lang::get('validation.custom.token_expired') : 'Token has expired', null, self::$responseCode::HTTP_UNAUTHORIZED);
            }

            $user = User::firstOrCreate(
                ['email' => $payload['email']],
                [
                    'name' => $payload['name'] ?? (Lang::has('validation.custom.default_user_name') ? Lang::get('validation.custom.default_user_name') : 'User'),
                    'email_verified_at' => now(),
                    'google_id' => $payload['sub'],
                ]
            );

            $tokenResult = $user->createToken('GoogleLoginToken');
            $accessToken = $tokenResult->accessToken;
            $refreshToken = $tokenResult->token->refresh_token ?? null;

            return response()->json([
                'token_type' => 'Bearer',
                'expires_in' => 1296000,
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
            ], self::$responseCode::HTTP_OK);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, self::$responseCode::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
