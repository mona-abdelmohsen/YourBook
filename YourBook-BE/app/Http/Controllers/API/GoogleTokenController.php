<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Google\Client as Google_Client;


class GoogleTokenController extends Controller
{
    // Verify the Google token and authenticate the user
    public function verifyGoogleToken(Request $request)
{
    $idToken = $request->input('id_token');

    if (!$idToken) {
        return response()->json(['error' => 'ID Token is missing'], 400);
    }

    try {
        // Verify token using Google's API
        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Invalid ID Token'], 401);
        }

        $payload = $response->json();

        // Validate claims
        if ($payload['iss'] !== 'https://accounts.google.com') {
            return response()->json(['error' => 'Invalid issuer'], 401);
        }

        if ($payload['aud'] !== env('GOOGLE_CLIENT_ID')) {
            return response()->json(['error' => 'Invalid audience'], 401);
        }

        if ($payload['exp'] < time()) {
            return response()->json(['error' => 'Token expired'], 401);
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

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'access_token' => $tokenResult->accessToken,
            'user' => $user,
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


}
