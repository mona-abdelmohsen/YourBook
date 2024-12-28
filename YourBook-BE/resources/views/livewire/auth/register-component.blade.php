

<div class="signup-wrapper">
    <!--Fake navigation-->
    <div class="fake-nav">
        <a href="/" class="logo">
            <img class="light-image" src="{{asset('assets/img/vector/logo/friendkit-bold.svg')}}" width="112" height="28" alt="" />
            <img class="dark-image" src="{{asset('assets/img/vector/logo/friendkit-white.svg')}}" width="112" height="28" alt="" />
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
                <div id="step-dot-2" class="dot is-second" data-step="25">
                    <i data-feather="user"></i>
                </div>
                <div id="step-dot-3" class="dot is-third" data-step="50">
                    <i data-feather="image"></i>
                </div>
                <div id="step-dot-4" class="dot is-fourth" data-step="75">
                    <i data-feather="lock"></i>
                </div>
                <div id="step-dot-5" class="dot is-fifth" data-step="100">
                    <i data-feather="flag"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="outer-panel">
        <div class="outer-panel-inner">
            <div class="process-title">
                <h2 id="step-title-1" class="step-title is-active">
                    Welcome, select an account type.
                </h2>
                <h2 id="step-title-2" class="step-title">Tell us more about you.</h2>
                <h2 id="step-title-3" class="step-title">Upload a profile picture.</h2>
                <h2 id="step-title-4" class="step-title">Secure your account.</h2>
                <h2 id="step-title-5" class="step-title">You're all set. Ready?</h2>
            </div>

            <div id="signup-panel-1" class="process-panel-wrap is-active">
                <div class="columns mt-4">
                    <div class="column is-4">
                        <div class="account-type">
                            <div class="type-image">
                                <img class="type-illustration" src="assets/img/illustrations/signup/type-1.svg" alt="" />
                                <img class="type-bg light-image" src="assets/img/illustrations/signup/type-1-bg.svg" alt="" />
                                <img class="type-bg dark-image" src="assets/img/illustrations/signup/type-1-bg-dark.svg" alt="" />
                            </div>
                            <h3>Company</h3>
                            <p>
                                Create a company account to be able to do some awesome things.
                            </p>
                            <a class="button is-fullwidth process-button" wire:click="test" data-step="step-dot-2">
                                Continue
                            </a>
                        </div>
                    </div>
                    <div class="column is-4">
                        <div class="account-type">
                            <div class="type-image">
                                <img class="type-illustration" src="assets/img/illustrations/signup/type-2.svg" alt="" />
                                <img class="type-bg light-image" src="assets/img/illustrations/signup/type-2-bg.svg" alt="" />
                                <img class="type-bg dark-image" src="assets/img/illustrations/signup/type-2-bg-dark.svg" alt="" />
                            </div>
                            <h3>Public Person</h3>
                            <p>Create a public account to be able to do some awesome things.</p>
                            <a class="button is-fullwidth process-button" data-step="step-dot-2">
                                Continue
                            </a>
                        </div>
                    </div>
                    <div class="column is-4">
                        <div class="account-type">
                            <div class="type-image">
                                <img class="type-illustration" src="assets/img/illustrations/signup/type-3.svg" alt="" />
                                <img class="type-bg light-image" src="assets/img/illustrations/signup/type-3-bg.svg" alt="" />
                                <img class="type-bg dark-image" src="assets/img/illustrations/signup/type-3-bg-dark.svg" alt="" />
                            </div>

                            <h3>Personal</h3>
                            <p>
                                Create a personal account to be able to do some awesome things.
                            </p>
                            <a class="button is-fullwidth process-button" data-step="step-dot-2">
                                Continue
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div id="signup-panel-2" class="process-panel-wrap is-narrow">
                <div class="form-panel">
                    <div class="field">
                        <label>First Name</label>
                        <div class="control">
                            <input type="text" class="input" placeholder="Enter your first name" />
                        </div>
                    </div>
                    <div class="field">
                        <label>Last Name</label>
                        <div class="control">
                            <input type="text" class="input" placeholder="Enter your last name" />
                        </div>
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <div class="control">
                            <input type="text" class="input" placeholder="Enter your email address" />
                        </div>
                    </div>
                </div>

                <div class="buttons">
                    <a class="button process-button" data-step="step-dot-1">Back</a>
                    <a class="button process-button is-next" data-step="step-dot-3">Next</a>
                </div>
            </div>

            <div id="signup-panel-3" class="process-panel-wrap is-narrow">
                <div class="form-panel">
                    <div class="photo-upload">
                        <div class="preview">
                            <a class="upload-button">
                                <i data-feather="plus"></i>
                            </a>
                            <img id="upload-preview" src="https://via.placeholder.com/150x150" data-demo-src="assets/img/avatars/avatar-w.png" alt="" />
                            <form id="profile-pic-dz" class="dropzone is-hidden" action="/"></form>
                        </div>
                        <div class="limitation">
                            <small>Only images with a size lower than 3MB are allowed.</small>
                        </div>
                    </div>
                </div>

                <div class="buttons">
                    <a class="button process-button" data-step="step-dot-2">Back</a>
                    <a class="button process-button is-next" data-step="step-dot-4">Next</a>
                </div>
            </div>

            <div id="signup-panel-4" class="process-panel-wrap is-narrow">
                <div class="form-panel">
                    <div class="field">
                        <label>Password</label>
                        <div class="control">
                            <input type="password" class="input" placeholder="Choose a password" />
                        </div>
                    </div>
                    <div class="field">
                        <label>Repeat Password</label>
                        <div class="control">
                            <input type="password" class="input" placeholder="Repeat your password" />
                        </div>
                    </div>
                    <div class="field">
                        <label>Phone Number</label>
                        <div class="control">
                            <input type="text" class="input" placeholder="Enter your phone number" />
                        </div>
                    </div>
                </div>

                <div class="buttons">
                    <a class="button process-button" data-step="step-dot-3">Back</a>
                    <a class="button process-button is-next" data-step="step-dot-5">Next</a>
                </div>
            </div>

            <div id="signup-panel-5" class="process-panel-wrap is-narrow">
                <div class="form-panel">
                    <img class="success-image" src="assets/img/illustrations/signup/mailbox.svg" alt="" />
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

@script
    <!-- Signup page js -->
    {{--        <script src="{{asset('assets/js/signup.js')}}"></script>--}}
    <script>
        /*! signup.js | Friendkit | © Css Ninja. 2019-2020 */

        /* ==========================================================================
        Signup Process JS
        ========================================================================== */

        // Dropzone.autoDiscover = false;

        $(function () {
            'use strict'


            window.addEventListener('alert', (event) => {
                console.log(event);
            })

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
                } else if (stepValue == '25') {
                    $('#signup-panel-2, #step-title-2').addClass('is-active')
                } else if (stepValue == '50') {
                    $('#signup-panel-3, #step-title-3').addClass('is-active')
                } else if (stepValue == '75') {
                    $('#signup-panel-4, #step-title-4').addClass('is-active')
                } else if (stepValue == '100') {
                    $('#signup-panel-5, #step-title-5').addClass('is-active')
                }
            })

            $('.process-button').on('click', function () {
                var $this = $(this)
                var targetStepDot = $this.attr('data-step')
                $this.addClass('is-loading')
                setTimeout(function () {
                    $this.removeClass('is-loading')
                    $('#' + targetStepDot).trigger('click')
                }, 800)
            })

            if ($('#profile-pic-dz').length) {
                var myDropzone = new Dropzone('#profile-pic-dz', {
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
                        var cropper = new Cropper(image, { aspectRatio: 1 })
                    },
                })
            }

            $('#signup-finish').on('click', function () {
                var $this = $(this)
                var url = '/navbar-v1-feed.html'
                $this.addClass('is-loading')
                setTimeout(function () {
                    window.location = url
                }, 800)
            })
        })

    </script>
@endscript
