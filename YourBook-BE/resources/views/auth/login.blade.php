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
                        <h3 class="form-subtitle">Enter your credentials to sign in.</h3>

                        <!--Form-->
                        <form class="login-form" method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-panel">
                                <div class="field">
                                    <label>Email</label>
                                    <div class="control">
                                        <x-text-input type="email" name="email" :value="old('email')" required
                                                      autocomplete="email" placeholder="Enter your email address"/>
                                    </div>
                                </div>
                                <div class="field">
                                    <label>Password</label>
                                    <div class="control">
                                        <x-text-input type="password" name="password" required
                                                      autocomplete="current-password"
                                                      placeholder="Enter your password"/>
                                    </div>
                                </div>
                                <div class="field is-flex">
                                    <div class="switch-block">
                                        <label class="f-switch">
                                            <input type="checkbox" class="is-switch"/>
                                            <i></i>
                                        </label>
                                        <div class="meta">
                                            <p>Remember me?</p>
                                        </div>
                                    </div>
                                    <a>Forgot Password?</a>
                                </div>
                            </div>

                            <div class="buttons">
                                <button type="submit" class="button is-solid primary-button is-fullwidth raised">Login
                                </button>
                            </div>

                            <div class="account-link has-text-centered">
                                <a href="{{route('register')}}">Don't have an account? Sign Up</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
