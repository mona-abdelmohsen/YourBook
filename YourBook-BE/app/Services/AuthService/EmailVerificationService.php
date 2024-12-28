<?php

namespace App\Services\AuthService;

use App\Models\Auth\User;
use App\Providers\RouteServiceProvider;
use App\Services\EmailVerificationServiceInterface;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class EmailVerificationService implements EmailVerificationServiceInterface
{

    public function sendEmailVerificationCode(User $user, bool $newData = false): void
    {
        if (! $user->hasVerifiedEmail()) {
            $user->sendMobileVerificationNotification($newData);
        }
    }

    public function verify(User $user, string $code): array
    {
        //Redirect user to dashboard if email already verified
        if ($user->hasVerifiedEmail()) return ['error' => '', 'success' => 'Verified.', 'verified' => true];

        // Code correct
        if ($code === $user->email_verify_code) {
            // check if code is still valid
            $secondsOfValidation = (int) config('mobile.seconds_of_validation');
            if ($secondsOfValidation > 0 &&  $user->email_verify_code_sent_at->diffInSeconds() > $secondsOfValidation) {
                $user->sendEmailVerificationNotification(true);
                return [
                    'error' => '',
                    'success' => 'Verification Code Expired, We sent you new code. please check your email.',
                    'verified' => false];
            }else {
                $user->markEmailAsVerified();
                return ['error' => '', 'success' => 'Verified.', 'verified' => true];
            }
        }

        if (config('mobile.max_attempts') > 0) {
            if ($user->email_attempts_left <= 1) {
                if($user->email_attempts_left == 1) $user->decrement('email_attempts_left');

                //check how many seconds left to get unbanned after no more attempts left
                $seconds_left = (int) config('mobile.attempts_ban_seconds') - $user->email_last_attempt_date->diffInSeconds();
                if ($seconds_left > 0) {
                    return ['error' => 'Your are banned, please wait '. $seconds_left.' Seconds', 'success' => '', 'verified' => false];
                }

                //Send new code and set new attempts when user is no longer banned
                $user->sendEmailVerificationNotification(true);
                return ['error' => '', 'success'=> 'New Activation Code sent to your email.', 'verified' => false];
            }

            $user->decrement('email_attempts_left');
            $user->forceFill(['email_last_attempt_date' => now()]);
            $user->save();
            return ['error' => 'Incorrect Code, you only have '.$user->email_attempts_left.' attempts left!.', 'success' => '', 'verified' => false];
        }

        return ['error' => 'Incorrect Code', 'success' => '', 'verified' => false];
    }

}
