@extends('components.layout.auth')
@section('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
    <style>
        .signup-wrapper .process-bar .progress-wrap .dot.is-second {
            left: calc(100% - 19px);
        }
    </style>
@endsection
@section('content')
    <div class="signup-wrapper" id="app-1">


        <div class="outer-panel">
            <div class="outer-panel-inner">
                <div class="process-title">
                    <h2 id="step-title-1" class="step-title is-active">
                        Verify your Email.
                    </h2>
                </div>
                <!-- Step 1 -->
                <div id="signup-panel-1" class="process-panel-wrap is-narrow is-active">
                    <div class="form-panel">
                        <p>
                            {{ __('Thanks for signing up! Before getting started, could you verify your email address by entering the verification code below? If you didn\'t receive the email, we will gladly send you another.') }}
                        </p>

                        <form action="{{route('verification.verify')}}" style="margin: 15px 0;" method="POST" id="codeForm">
                            @csrf
                            <div class="field">
                                <label class="label">Code</label>
                                <div class="control">
                                    <input class="input" type="text" placeholder="000000" name="code">
                                </div>
                            </div>
                        </form>

                        @if (session('success'))
                            <div class="my-5 has-background-primary has-text-white card card-content">
                                {{session('success')}}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="my-5 has-background-danger-light has-text-danger card card-content">
                                {{session('error')}}
                            </div>
                        @endif
                        @if (session('status') == 'verification-code-sent')
                            <div class="my-5 has-background-primary has-text-white card card-content">
                                {{ __('Verification code has been sent to the email address: '.auth()->user()->email) }}
                            </div>
                        @endif

                        @if($errors->has('code'))
                            <div class="my-5 has-background-danger-light has-text-danger card card-content">
                                {{$errors->first('code')}}
                            </div>
                        @endif
                        <form method="POST" id="resend" action="{{ route('verification.send') }}">
                            @csrf
                        </form>

                        <form method="POST" id="logout" action="{{ route('logout') }}">
                            @csrf
                        </form>

                    </div>
                    <div class="buttons">
                        <a class="button process-button is-next" onclick="document.getElementById('codeForm').submit()">
                            Verify</a>
                        <a class="button process-button is-next" onclick="document.getElementById('resend').submit()">Resend
                            Verification Email</a>
                        <a class="button close-button" onclick="document.getElementById('logout').submit()">Log Out</a>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
