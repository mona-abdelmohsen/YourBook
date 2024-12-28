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
        <!--Fake navigation-->
        <div class="fake-nav">
            <a href="/" class="logo">
                <img class="light-image" src="{{asset('assets/img/vector/logo/friendkit-bold.svg')}}" width="112"
                     height="28" alt=""/>
                <img class="dark-image" src="{{asset('assets/img/vector/logo/friendkit-white.svg')}}" width="112"
                     height="28" alt=""/>
            </a>
        </div>


        <div class="process-bar-wrap">
            <div class="process-bar">
                <div class="progress-wrap">
                    <div class="track"></div>
                    <div class="bar"></div>
                    <div id="step-dot-1" class="dot is-first is-active is-current" data-step="1">
                        <i data-feather="smartphone"></i>
                    </div>
                    <div id="step-dot-2" class="dot is-second" data-step="2">
                        <i data-feather="shield"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="outer-panel">
            <div class="outer-panel-inner">
                <div class="process-title">
                    <h2 id="step-title-1" class="step-title is-active">
                        Verify your mobile number.
                    </h2>
                    <h2 id="step-title-2" class="step-title">Enter Verification Code.</h2>
                </div>
                <!-- Step 1 -->
                <div id="signup-panel-1" class="process-panel-wrap is-narrow is-active">
                    <div class="form-panel">
                        <p>
                            Thanks for signing up! Before getting started, you need to verify your mobile phone number.
                        </p>
                        <div class="field">
                            <label>Phone</label>
                            <div class="control">
                                <input type="tel" class="input" id="phone" style="width:100%"/>
                            </div>
                        </div>
                        <div id="recaptcha-container"></div>

                    </div>
                    <div class="buttons">
                        <a class="button process-button is-next" v-on:click="goStep($event)" id="send_otp"
                           style="display:none;" data-step="step-dot-2">Send OTP</a>
                    </div>
                </div>

                <!-- Step 2 -->
                <div id="signup-panel-2" class="process-panel-wrap is-narrow">
                    <div class="form-panel">
                        <p>
                            Please enter the OTP sent to your number: @{{phone_number}}
                        </p>
                        <div class="field">
                            <label>Code</label>
                            <div class="control">
                                <input type="text" class="input" v-model="code" placeholder="Enter OTP Code"/>
                            </div>
                        </div>
                    </div>

                    <div class="buttons">
                        <a class="button process-button" v-on:click="goStep($event)" data-step="step-dot-1">Back</a>
                        <a class="button process-button is-next" v-on:click="verify">Verify</a>
                    </div>
                </div>

            </div>
        </div>

        <!--Edit Credit card Modal-->
        <div id="crop-modal" class="modal is-small crop-modal is-animated">
            <div class="modal-background"></div>
            <div class="modal-content">
                <div class="modal-card">
                    <header class="modal-card-head">
                        <h3>Crop your picture</h3>
                        <div class="close-wrap">
                            <button class="close-modal" aria-label="close">
                                <i data-feather="x"></i>
                            </button>
                        </div>
                    </header>
                    <div class="modal-card-body">
                        <div id="cropper-wrapper" class="cropper-wrapper"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection


@section('jscode')
    @if(env('APP_ENV') == 'local')
        <script src="{{asset('js/vue-dev.js')}}"></script>
    @else
        <script src="{{asset('js/vue-prod.js')}}"/></script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{asset('js/validate.min.js')}}"></script>
    <script src="{{asset('js/moment-with-locales.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
    <script src="https://www.gstatic.com/firebasejs/6.0.2/firebase.js"></script>

    <script>

        var app = new Vue({
            el: '#app-1',
            data: {
                currentStep: 'step-dot-1',
                phone: null,
                code: '',
                firebase: null,
            },
            computed: {
                phone_number: function () {
                    if (this.phone) {
                        return this.phone.getNumber();
                    }
                    return '';
                },
            },
            mounted() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{csrf_token()}}'
                    }
                });


                this.phone = document.querySelector("#phone");
                this.phone = new intlTelInput(this.phone, {
                    initialCountry: "auto",
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js",
                });

                this.phone.setNumber('{{auth()->user()->phone}}');

                $(function () {
                    'use strict'

                    app.initFireBase();

                    window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                        'size': 'normal',
                        'callback': (response) => {
                            $("#send_otp").show();
                        },
                        'expired-callback': () => {
                            $("#send_otp").hide();
                        }
                    });
                    recaptchaVerifier.render();


                    $('.progress-wrap .dot').on('click', function () {
                        var $this = $(this)
                        var stepValue = $this.attr('data-step')
                        $this
                            .closest('.progress-wrap')
                            .find('.bar')
                            .css('width', stepValue + '%')
                        $this.siblings('.dot').removeClass('is-current')
                        $this.addClass('is-active is-current')
                        $this.prevAll('.dot').addClass('is-active')
                        $this.nextAll('.dot').removeClass('is-active')

                        $('.process-panel-wrap').removeClass('is-active')
                        $('.step-title').removeClass('is-active')

                        if (stepValue == '1') {
                            $('#signup-panel-1, #step-title-1').addClass('is-active')
                        } else if (stepValue == '2') {
                            $('#signup-panel-2, #step-title-2').addClass('is-active')
                        }
                    })


                })

                validate.extend(validate.validators.datetime, {
                    // The value is guaranteed not to be null or undefined but otherwise it
                    // could be anything.
                    parse: function (value, options) {
                        return +moment.utc(value);
                    },
                    // Input is a unix timestamp
                    format: function (value, options) {
                        var format = options.dateOnly ? "YYYY-MM-DD" : "YYYY-MM-DD hh:mm:ss";
                        return moment.utc(value).format(format);
                    }
                });
            },
            methods: {
                initFireBase: function () {
                    // Your web app's Firebase configuration
                    const firebaseConfig = {
                        apiKey: "AIzaSyBzOJ0E0tPC0ubihMGhWYVJdxuWMAv2fYs",
                        authDomain: "yourbook-96c31.firebaseapp.com",
                        projectId: "yourbook-96c31",
                        storageBucket: "yourbook-96c31.appspot.com",
                        messagingSenderId: "386535978125",
                        appId: "1:386535978125:web:378187322c64077d031ec4"
                    };

                    // Initialize Firebase
                    app.firebase = firebase.initializeApp(firebaseConfig);

                },
                verify: async function (event) {
                    var $this = $(event.target)
                    $this.addClass('is-loading')

                    window.confirmationResult.confirm(app.code).then(function (result) {
                        var user = result.user;
                        app.alert('sussecc', 'Mobile Verified, redirecting...', 4000);

                        $this.removeClass('is-loading');

                        $.ajax({
                            type: 'POST',
                            data: {
                                code: app.code,
                                phone: app.phone.getNumber(),
                            },
                            url: '{{route('verification.verify-mobile')}}',
                            success: function (res) {
                                window.location.replace('{{route('dashboard')}}');
                            },
                            error: function (error) {
                                $this.removeClass('is-loading');
                                console.log(error);
                            },
                        });

                    }).catch(function (error) {
                        app.alert('error', error.message, 5000);
                        $this.removeClass('is-loading');
                    });
                },
                sendOTP: async function () {
                    return firebase.auth().signInWithPhoneNumber(app.phone.getNumber(), window.recaptchaVerifier).catch(function (error) {
                        app.alert('error', error.message, 5000);
                    });
                },
                validation: async function () {
                    switch (this.currentStep) {
                        case 'step-dot-1':
                            validation = {};
                            // offline validation..
                            if (!app.phone.isValidNumber()) {
                                validation = {
                                    ...validation,
                                    phone: ['Invalid phone number.']
                                }
                            }

                            if (!validate.isEmpty(validation)) {
                                return validation;
                            }

                            if (app.phone.getNumber() != '{{auth()->user()->phone}}') {
                                // online validation
                                return $.ajax({
                                    type: 'POST',
                                    data: {
                                        phone: app.phone.getNumber(),
                                    },
                                    url: '{{url('/utilities/email-phone-validator')}}',
                                });
                            }
                            break;
                    }

                    return {};
                },
                setDialCode: function () {
                    let country = this.countries.filter(function (item) {
                        return item.id == app.country_id;
                    })[0];
                    this.phone.setCountry(country.code);
                },
                goStep: async function (event) {
                    var $this = $(event.target)
                    var targetStepDot = $this.attr('data-step')
                    $this.addClass('is-loading')

                    let validation = {};
                    validation = await app.validation();


                    if (!validate.isEmpty(validation)) {
                        $this.removeClass('is-loading');
                        this.alert('error', Object.values(validation)[0][0], 4000);
                        return;
                    }

                    if (this.currentStep = 'step-dot-1') {
                        app.sendOTP().then(function (confirmationResult) {
                            window.confirmationResult = confirmationResult;
                            coderesult = confirmationResult;
                            console.log(coderesult);

                        });
                    }

                    $this.removeClass('is-loading');
                    $('#' + targetStepDot).trigger('click');
                    this.currentStep = targetStepDot;

                },
                alert: function (type, message, time_ms) {
                    // toasts.service[type](
                    //     '',
                    //     'mdi mdi-progress-check',
                    //     message,
                    //     'topRight',
                    //     time_ms,
                    // );

                    Swal.fire({
                        toast: true,
                        position: 'top-right',
                        timerProgressBar: true,
                        timer: time_ms,
                        text: message,
                        icon: type,
                        showConfirmButton: false,
                        showCloseButton: true,
                    })

                },
            },
        });

    </script>


    <!-- Signup page js -->
    <script>

        // Dropzone.autoDiscover = false;


    </script>
@endsection
