<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Services\EmailVerificationServiceInterface;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\ValidationException;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     * @throws ValidationException
     */
    public function __invoke(Request $request, EmailVerificationServiceInterface $emailVerificationService): RedirectResponse
    {

        $this->validate($request, [
            'code'  => 'required|numeric',
        ]);

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
        }

        $status = $emailVerificationService->verify(auth()->user(), $request->code);

        if ($status['verified'] && $request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
            return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
        }

        return back()->with($status);
    }
}
