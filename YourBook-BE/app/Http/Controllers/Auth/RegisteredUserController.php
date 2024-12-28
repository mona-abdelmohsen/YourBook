<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Services\DropzoneInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{

    /**
     * Display the registration view.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * @throws ValidationException
     */
    public function emailPhoneValidator(Request $req){
        $validator = Validator::make($req->all(), [
            'email' => 'unique:users',
            'phone' => 'unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 200);
        }

        return response()->json(null, 200);

    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, DropzoneInterface $dropzone)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'country_id'    => ['required', 'exists:countries,id'],
            'phone'         => ['required'],
            'gender'        => ['required', 'in:male,female'],
            'date_birth'    => ['required', 'date'],
            'profilePicture'    => ['required'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Upload Profile Picture...
        $dropzone->moveFromTemp($request->profilePicture, 'public/avatars/'.$request->profilePicture);

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'country_id'    => $request->country_id,
            'phone'         => $request->phone,
            'gender'        => $request->gender,
            'birth_date'    => $request->date_birth,
            'avatar'        => 'public/avatars/'.$request->profilePicture,
            'password'      => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return response()->json($user, 201);
        //return redirect(RouteServiceProvider::HOME);
    }
}
