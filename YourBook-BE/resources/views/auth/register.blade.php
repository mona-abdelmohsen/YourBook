@extends('components.layout.auth')
@section('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
    <style>
        .signup-wrapper .process-bar .progress-wrap .dot.is-second {
            left: calc(40% - 19px);
        }

        .signup-wrapper .process-bar .progress-wrap .dot.is-third {
            left: calc(80% - 19px);
        }

        .signup-wrapper .process-bar .progress-wrap .dot.is-fourth {
            left: calc(100% - 19px);
        }
    </style>
@endsection
@section('content')
    <div class="signup-wrapper" id="app-1">
        <!--Fake navigation-->
        <div class="fake-nav">
            <a href="/" class="logo">
                <img class="light-image" src="{{asset('assets/img/navbar/logo.svg')}}" width="112" height="28"
                     alt=""/>
                <img class="dark-image" src="{{asset('assets/img/navbar/logo.svg')}}" width="112" height="28"
                     alt=""/>
            </a>
        </div>


        <div class="process-bar-wrap">
            <div class="process-bar">
                <div class="progress-wrap">
                    <div class="track"></div>
                    <div class="bar"></div>
                    <div id="step-dot-1" class="dot is-first is-active is-current" data-step="0">
                        <i data-feather="smile"></i>
                    </div>
                    <div id="step-dot-2" class="dot is-second" data-step="40">
                        <i data-feather="image"></i>
                    </div>
                    <div id="step-dot-3" class="dot is-third" data-step="80">
                        <i data-feather="lock"></i>
                    </div>
                    <div id="step-dot-4" class="dot is-fourth" data-step="100">
                        <i data-feather="flag"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="outer-panel">
            <div class="outer-panel-inner">
                <div class="process-title">
                    <h2 id="step-title-1" class="step-title is-active">
                        Welcome, Tell us about you.
                    </h2>
                    <h2 id="step-title-2" class="step-title">Upload a profile picture.</h2>
                    <h2 id="step-title-3" class="step-title">Choose your password.</h2>
                    <h2 id="step-title-4" class="step-title">You're all set. Ready?</h2>
                </div>
                <!-- Step 1 -->
                <div id="signup-panel-1" class="process-panel-wrap is-narrow is-active">
                    <div class="form-panel">
                        <div class="field">
                            <label>Name</label>
                            <div class="control">
                                <input type="text" v-model="name" class="input" placeholder="Enter your Full name"/>
                            </div>
                        </div>
                        <div class="field">
                            <label>Email</label>
                            <div class="control">
                                <input type="email" v-model="email" class="input"
                                       placeholder="Enter your email address"/>
                            </div>
                        </div>
                        <div class="field">
                            <label>Country</label>
                            <div class="control has-icons-left">
                                <div class="select" style="width:100%;">
                                    <select style="width:100%;" v-model="country_id" v-on:change="setDialCode">
                                        <option value="" selected>Country</option>
                                        <option v-for="i in countries" :value="i.id">
                                            @{{ i.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="icon is-small is-left">
                                    <i data-feather="globe"></i>
                                </div>
                            </div>
                        </div>
                        <div class="field">
                            <label>Phone</label>
                            <div class="control">
                                <input type="tel" class="input" id="phone" style="width:100%"/>
                            </div>
                        </div>
                        <div class="columns">
                            <div class="column">
                                <div class="field">
                                    <label>Gender</label>
                                    <div class="control">
                                        <div class="select" style="width:100%;">
                                            <select style="width:100%;" v-model="gender">
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="column">
                                <div class="field" style="height:100%;">
                                    <label>Birth Date</label>
                                    <div class="control">
                                        <input type="date" v-model="date_birth" class="input">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="buttons">
                        <a class="button process-button is-next" v-on:click="goStep($event)"
                           data-step="step-dot-2">Next</a>
                    </div>
                </div>

                <!-- Step 2 -->
                <div id="signup-panel-2" class="process-panel-wrap is-narrow">
                    <div class="form-panel">
                        <div class="photo-upload">
                            <div class="preview">
                                <a class="upload-button">
                                    <i data-feather="plus"></i>
                                </a>
                                <img id="upload-preview" src="https://via.placeholder.com/150x150"
                                     data-demo-src="https://via.placeholder.com/150x150" alt=""/>
                                <form id="profile-pic-dz" class="dropzone is-hidden" action="/"></form>
                            </div>
                            <div class="limitation">
                                <small>Only images with a size lower than 3MB are allowed.</small>
                            </div>
                        </div>
                    </div>


                    <div class="buttons">
                        <a class="button process-button" v-on:click="goStep($event)" data-step="step-dot-1">Back</a>
                        <a class="button process-button is-next" v-on:click="goStep($event)"
                           data-step="step-dot-3">Next</a>
                    </div>
                </div>

                <!-- Step 3 -->
                <div id="signup-panel-3" class="process-panel-wrap is-narrow">
                    <div class="form-panel">
                        <div class="field">
                            <label>Password</label>
                            <div class="control">
                                <input type="password" class="input" v-model="password"
                                       placeholder="Choose a password"/>
                            </div>
                        </div>
                        <div class="field">
                            <label>Repeat Password</label>
                            <div class="control">
                                <input type="password" class="input" v-model="password_conf"
                                       placeholder="Repeat your password"/>
                            </div>
                        </div>
                    </div>
                    <div class="buttons">
                        <a class="button process-button" v-on:click="goStep($event)" data-step="step-dot-2">Back</a>
                        <a class="button process-button is-next" v-on:click="goStep($event)"
                           data-step="step-dot-4">Next</a>
                    </div>
                </div>

                <!-- Step 4 -->
                <div id="signup-panel-4" class="process-panel-wrap is-narrow">

                    <div class="form-panel">
                        <img class="success-image" src="{{asset('assets/img/illustrations/signup/mailbox.svg')}}"
                             alt=""/>
                        <div class="success-text">
                            <h3>Congratz, you successfully created your account.</h3>
                            <p>
                                We just sent you a confirmation email. PLease confirm your account
                                within 24 hours.
                            </p>
                            <a id="signup-finish" class="button is-fullwidth">Let Me In</a>
                        </div>
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

    <script>

        var app = new Vue({
            el: '#app-1',
            data: {
                countries: [],
                currentStep: 'step-dot-1',

                name: '',
                email: '',
                country_id: '',
                phone: '',
                gender: '',
                date_birth: '',
                profilePicture: '',
                password: '',
                password_conf: '',
            },
            mounted() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{csrf_token()}}'
                    }
                });

                this.getCountries();

                this.phone = document.querySelector("#phone");
                this.phone = new intlTelInput(this.phone, {
                    initialCountry: "auto",
                    geoIpLookup: function () {
                        return {'country': 'EG'}
                    },
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js",
                });

                $(function () {
                    'use strict'

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

                        if (stepValue == '0') {
                            $('#signup-panel-1, #step-title-1').addClass('is-active')
                        } else if (stepValue == '40') {
                            $('#signup-panel-2, #step-title-2').addClass('is-active')
                        } else if (stepValue == '80') {
                            $('#signup-panel-3, #step-title-3').addClass('is-active')
                        } else if (stepValue == '100') {
                            $('#signup-panel-4, #step-title-4').addClass('is-active')
                        }
                    })


                    if ($('#profile-pic-dz').length) {
                        var myDropzone = new Dropzone('#profile-pic-dz', {
                            url: "{{route('dropzone.handler')}}", // Set the url for your upload script location
                            paramName: "file", // The name that will be used to transfer the file
                            maxFiles: 1,
                            maxFilesize: 8, // MB
                            acceptedFiles: '.jpeg,.jpg,.png',
                            clickable: '.upload-button',
                            init: function () {
                                this.on('error', function (file, message) {
                                    console.log(message)
                                    this.removeFile(file)
                                })
                                if (this.files[1] != null) {
                                    this.removeFile(this.files[0])
                                }
                            },
                            success: function (file, response) {
                                console.log(response);
                                app.profilePicture = response;
                            },
                            transformFile: function (file, done) {
                                $('#crop-modal').addClass('is-active')
                                //pictures = [];
                                // Create the image editor overlay
                                var editor = document.createElement('div')
                                editor.style.position = 'absolute'
                                editor.style.left = 0
                                editor.style.right = 0
                                editor.style.top = 0
                                editor.style.bottom = 0
                                editor.style.zIndex = 9999
                                editor.style.backgroundColor = '#fff'
                                document.getElementById('cropper-wrapper').appendChild(editor)

                                // Create confirm button at the top left of the viewport
                                var buttonConfirm = document.createElement('button')
                                buttonConfirm.style.position = 'absolute'
                                buttonConfirm.style.right = '10px'
                                buttonConfirm.style.bottom = '10px'
                                buttonConfirm.style.zIndex = 9999
                                buttonConfirm.textContent = 'Crop'
                                buttonConfirm.classList.add('button')
                                editor.appendChild(buttonConfirm)

                                buttonConfirm.addEventListener('click', function () {
                                    // Get the canvas with image data from Cropper.js
                                    var canvas = cropper.getCroppedCanvas({
                                        width: 256,
                                        height: 256,
                                    })

                                    // Turn the canvas into a Blob (file object without a name)
                                    canvas.toBlob(function (blob) {
                                        // Create a new Dropzone file thumbnail
                                        myDropzone.createThumbnail(
                                            blob,
                                            myDropzone.options.thumbnailWidth,
                                            myDropzone.options.thumbnailHeight,
                                            myDropzone.options.thumbnailMethod,
                                            false,
                                            function (dataURL) {
                                                // Update the Dropzone file thumbnail
                                                myDropzone.emit('thumbnail', file, dataURL)
                                                // Return the file to Dropzone
                                                done(blob)
                                                //console.log(blob);

                                                //Display image preview
                                                var previewReader = new FileReader()
                                                previewReader.onload = function (event) {
                                                    // event.target.result contains base64 encoded image
                                                    $('#upload-preview').attr('src', blob.dataURL)
                                                    //Show popover
                                                    $('.picture-container')
                                                        .webuiPopover({
                                                            title: '',
                                                            content:
                                                                'Your photo is ready to be uploaded. Hit the "Save Changes" button to complete the upload process.',
                                                            animation: 'pop',
                                                            width: 300,
                                                            style: 'inverse',
                                                            placement: 'top',
                                                            offsetTop: -16,
                                                        })
                                                        .trigger('click')

                                                    //console.log('THIS IS THE BLOB', blob)
                                                }
                                                previewReader.readAsDataURL(file)
                                            },
                                        )

                                        var reader = new FileReader()

                                        reader.addEventListener('loadend', function (event) {
                                            // put picture in a holding var
                                            /*pictures.push({
                                                              binaryData: btoa(reader.result),
                                                              filePath: file.name,
                                                              seoFilename: file.name.substring(0, file.name.lastIndexOf(".")),
                                                              titleAttribute: file.name,
                                                              altAttribute: file.name,
                                                              mimeType: file.type,
                                                              isNew: true
                                                          });*/
                                            // accept the file
                                            //done();
                                            //console.log('THIS IS THE RESULT', reader.result);
                                            //console.log('THIS IS THE ARRAY', pictures);
                                        })
                                        //reader.readAsBinaryString(file);
                                        reader.readAsBinaryString(blob)
                                    })

                                    // Remove the editor from the view
                                    document.getElementById('cropper-wrapper').removeChild(editor)
                                    document.getElementById('crop-modal').classList.remove('is-active')
                                })

                                // Create an image node for Cropper.js
                                var image = new Image()
                                image.src = URL.createObjectURL(file)
                                editor.appendChild(image)

                                // Create Cropper.js
                                var cropper = new Cropper(image, {aspectRatio: 1})
                            },
                        })
                    }


                    /** On Register Complete :) */
                    $('#signup-finish').on('click', function () {
                        var $this = $(this)
                        var url = '{{route('login')}}'
                        $this.addClass('is-loading')
                        setTimeout(function () {
                            window.location = url
                        }, 800)
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
                createUser: async function () {
                    return $.ajax({
                        type: 'POST',
                        data: {
                            name: app.name,
                            email: app.email,
                            country_id: app.country_id,
                            phone: app.phone.getNumber(),
                            gender: app.gender,
                            date_birth: app.date_birth,
                            profilePicture: app.profilePicture.name,
                            password: app.password,
                            password_confirmation: app.password_conf,
                        },
                        url: '{{url('/register')}}',
                    });
                },
                validation: async function () {
                    switch (this.currentStep) {
                        case 'step-dot-1':
                            // offline validation..
                            var constraints = {
                                name: {
                                    length: {
                                        minimum: 1,
                                        tooShort: "^needs to have %{count} words or more",
                                    },
                                },
                                email: {
                                    email: true
                                },
                                country: {
                                    numericality: {
                                        onlyInteger: true,
                                        greaterThan: 0,
                                        message: "^please select your country."
                                    }
                                },
                                gender: {
                                    inclusion: {
                                        within: {"male": "m", "female": "f"},
                                        message: "^please select the gender.",
                                    }
                                },
                                date_birth: {
                                    datetime: {
                                        dateOnly: true,
                                        latest: moment.utc().subtract(9, 'years'),
                                        message: "^You need to be at least 9 years old"
                                    }
                                },
                            };
                            var validation = validate({
                                name: app.name,
                                email: app.email,
                                country: app.country_id,
                                gender: app.gender,
                                date_birth: app.date_birth,
                            }, constraints);

                            if (!app.phone.isValidNumber()) {
                                validation = {
                                    ...validation,
                                    phone: ['Invalid phone number.']
                                }
                            }

                            if (!validate.isEmpty(validation)) {
                                return validation;
                            }

                            // online validation
                            return $.ajax({
                                type: 'POST',
                                data: {
                                    email: app.email,
                                    phone: app.phone.getNumber(),
                                },
                                url: '{{url('/utilities/email-phone-validator')}}',
                            });
                        case 'step-dot-2':
                            if (validate.isEmpty(app.profilePicture)) {
                                return {
                                    'profile_picture': ['Please, choose a profile picture.'],
                                }
                            }
                            break;
                        case 'step-dot-3':
                            var constraints = {
                                password: {
                                    presence: true,
                                    length: {
                                        minimum: 8,
                                        message: 'must be at least 8 characters'
                                    }
                                },
                                password_conf: {
                                    presence: true,
                                    equality: {
                                        attribute: 'password',
                                        message: '^Confirmation does not match the password'
                                    }
                                }
                            };
                            var validation = validate({
                                password: app.password,
                                password_conf: app.password_conf
                            }, constraints);

                            let regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
                            let match_ = regex.test(app.password);

                            if (!match_) {
                                validation = {
                                    ...validation,
                                    password: ['Password should contain at least one upper case, lower case letters, number and special character']
                                }
                            }
                            return validation;
                    }

                    return {};
                },
                setDialCode: function () {
                    let country = this.countries.filter(function (item) {
                        return item.id == app.country_id;
                    })[0];
                    this.phone.setCountry(country.code);
                },
                getCountries: function () {
                    {{--https://{{BaseURL}}/api/v1/utilities/get-countries--}}
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': '{{csrf_token()}}'
                        }
                    });
                    $.ajax({
                        type: 'GET',
                        url: '{{url('/api/v1/utilities/get-countries')}}',
                        success: function (res) {
                            app.countries = res.data;
                        }
                    })
                },
                goStep: async function (event) {
                    var $this = $(event.target)
                    var targetStepDot = $this.attr('data-step')
                    $this.addClass('is-loading')

                    let validation = {};
                    validation = await app.validation();


                    if (!validate.isEmpty(validation)) {
                        this.alert('error', Object.values(validation)[0][0], 4000);
                        $this.removeClass('is-loading');
                        return;
                    }

                    // 3 => password step
                    if (this.currentStep == 'step-dot-3') {
                        const response = await app.createUser();
                        app.user_id = response.id;
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
