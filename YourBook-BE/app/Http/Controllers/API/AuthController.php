<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Auth\OauthAccessTokens;
use App\Models\Auth\OauthRefreshTokens as OauthTokens;
use App\Models\Auth\User;
use App\Models\Auth\Admin;
use App\Providers\RouteServiceProvider;
use App\Services\EmailVerificationServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->toArray(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        return $this->success("User Registered", $user, self::$responseCode::HTTP_CREATED);
    }

    public function logout(): JsonResponse
    {
        $accessToken = Auth::user()->token();
        if ($accessToken) {

            Auth::user()->update([
                'fcm_token' => null,
            ]);

            $query = OauthTokens::where('access_token_id', '=', $accessToken->id);
            if ($query) {
                $query->update([
                    'revoked' => true
                ]);
                $accessToken->revoke();
                return response()->json([
                    'data' => [],
                    'errors' => [
                        'message' => '',
                    ]
                ], 200);
            } else {
                return response()->json(null, 204);
            }
        } else {
            return response()->json(null, 204);
        }
    }

    public function sendResetLinkEmail(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $validator = Validator::make($request->toArray(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = Password::broker()->sendResetLink(
            $request->only('email')
        );


        return $response == Password::RESET_LINK_SENT
            ? $this->success("Reset Link Sent", null, self::$responseCode::HTTP_OK)
            : $this->error(trans($response), ['email' => [trans($response)]], self::$responseCode::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->toArray(), [
            'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if (!Hash::check($request->input('current_password'), Auth::user()->password)) {
            return $this->error(
                'the old password is incorrect.',
                ['current_password' => ['the old password is incorrect.']],
                self::$responseCode::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        $user = User::find(Auth::user()->id);
        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return $this->success("Password Updated", null, self::$responseCode::HTTP_OK);
    }


    /**
     * @return JsonResponse
     */
    public function sendEmailVerificationCode(Request $request): JsonResponse
    {
        if (!$request->user()->hasVerifiedEmail()) {
            $request->user()->sendEmailVerificationNotification();
            return $this->success('Verification Code Sent to Email', null, self::$responseCode::HTTP_OK);
        }
        return $this->error('Account Email is Verified.', null, self::$responseCode::HTTP_UNAUTHORIZED);
    }


    /**
     * @param Request $request
     * @param EmailVerificationServiceInterface $emailVerificationService
     * @return JsonResponse
     */
    public function emailVerify(Request $request, EmailVerificationServiceInterface $emailVerificationService): JsonResponse
    {
        $validator = Validator::make($request->toArray(), [
            'code' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $status = $emailVerificationService->verify(auth()->user(), $request->code);

        if ($status['verified'] && $request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
            return $this->success('Email Verified.', null, self::$responseCode::HTTP_OK);
        }

        if ($status['success']) {
            return $this->error($status['error'], null, self::$responseCode::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $this->error($status['error'], null, self::$responseCode::HTTP_UNPROCESSABLE_ENTITY);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function setToken(Request $request): JsonResponse
    {
        $token = $request->input('fcm_token');
        $request->user()->update([
            'fcm_token' => $token
        ]);
        return $this->success('Successfully Updated FCM Token', null, self::$responseCode::HTTP_CREATED);
    }


    // Delete Account

    public function deleteAccount(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user) {
            // Revoke all tokens for the user
            $user->tokens()->delete(); 

            // Soft delete the user
            $user->delete();

            return $this->success('Account successfully deleted.', null, 200);
        }

        return $this->success('User not found.', null, 404);
    }

    // disable users
    public function disableUser($user_id)
{
    $user = User::findOrFail($user_id);
    $admin = Admin::find(Auth::id());
    
    if (!$admin) {
        return $this->error('You are not authorized to delete a user.', null, 403);
    }

    if ($user) {
        $user->delete(); 
        return $this->success('User account successfully deleted.', null, 200);
    }

    return $this->error('User not found.', null, 404);
}

    // enable user
    public function enableUser($user_id)
{
    $admin = Admin::find(Auth::id()); 

    if (!$admin) {
        return $this->error('You are not authorized to restore a user.', null, 403);
    }

    $user = User::withTrashed()->find($user_id); 

    if ($user && $user->trashed()) {
        $user->restore(); 
        return $this->success('User account successfully restored.', null, 200);
    }

    return $this->error('User not found or not deleted.', null, 404);
}


}
