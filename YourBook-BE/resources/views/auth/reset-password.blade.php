@extends('components.layout.auth')
@section('content')
    <div class="signup-wrapper">
        <!--Fake navigation-->
        <div class="fake-nav">
            <a href="/" class="logo">
                <img class="light-image" src="{{asset('assets/img/navbar/logo.svg')}}" width="112" height="28"
                     alt=""/>
                <img class="dark-image" src="{{asset('assets/img/navbar/logo.svg')}}" width="112" height="28"
                     alt=""/>
            </a>
        </div>

        <div class="container">
            <div class="login-container">
                <div class="columns is-vcentered">
                    <div class="column is-6 image-column">
                        <!--Illustration-->
                        <img class="light-image login-image" src="{{asset('assets/img/illustrations/login/login.svg')}}"
                             alt=""/>
                        <img class="dark-image login-image"
                             src="{{asset('assets/img/illustrations/login/login-dark.svg')}}" alt=""/>
                    </div>
                    <div class="column is-6">
                        <h2 class="form-title">Welcome Back</h2>
                        <h3 class="form-subtitle">Enter your credentials to Reset your password.</h3>

                        <form method="POST" action="{{ route('password.store') }}">
                            @csrf
                    
                            <!-- Password Reset Token -->
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">
                            <input type="hidden" name="email" value="{{ $request->email }}">
                    
                          
                            <!-- Password -->
                            <div class="mt-4">
                                <x-input-label for="password" :value="__('Password')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                    
                            <!-- Confirm Password -->
                            <div class="mt-4">
                                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    
                                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                                    type="password"
                                                    name="password_confirmation" required autocomplete="new-password" />
                    
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                    
                            <div class="buttons my-2">
                                <button type="submit" class="button is-solid primary-button is-fullwidth raised"> {{__('Reset Password')}}
                                </button>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
