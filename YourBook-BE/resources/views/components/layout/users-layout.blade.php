<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta http-equiv="x-ua-compatible" content="ie=edge"/>

    <title>{{$pageTitle ?? env('APP_NAME')}} </title>
    <link rel="icon" type="image/svg" href="{{asset('assets/img/navbar/logo.svg')}}"/>

    <!-- Google Tag Manager -->
    <script>
        ;
        (function (w, d, s, l, i) {
            w[l] = w[l] || []
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            })
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : ''
            j.async = true
            j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl
            f.parentNode.insertBefore(j, f)
        })(window, document, 'script', 'dataLayer', 'GTM-KQHJPZP')
    </script>
    <!-- End Google Tag Manager -->

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:600,700,800,900" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/fontisto@v3.0.4/css/fontisto/fontisto-brands.min.css" rel="stylesheet"/>
    <!-- Core CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/app.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/css/core.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/css/globalClasses.css')}}"/>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .uploader-config {
            --darkmode: 0;
            --h-accent: 223;
            --s-accent: 100%;
            --l-accent: 61%;
        }
        .select2 {
            width: 100% !important;
        }
    </style>

    {{$head ?? ''}}
</head>

<body>
<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KQHJPZP" height="0" width="0"
            style="display: none; visibility: hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->

<!-- Page loader -->
<div class="pageloader"></div>
<div class="infraloader is-active"></div>
<div class="app-overlay"></div>

@include('components.include.menu')

<!-- Content Wrapper -->
<div class="view-wrapper">
    {{ $slot }}
</div>


<!-- Explore Model -->
<div class="explorer-menu">
    <div class="explorer-inner">
        <div class="explorer-container">
            <!--Header-->
            <div class="explorer-header">
                <h3>Explore</h3>
                <div class="control">
                    <input type="text" class="input is-rounded is-fade" placeholder="Filter"/>
                    <div class="form-icon">
                        <i data-feather="filter"></i>
                    </div>
                </div>
            </div>
            <!--List-->
            <div class="explore-list has-slimscroll">
                <!--item-->
                <a href="/navbar-v1-feed.html" class="explore-item">
                    <img src="../assets/img/icons/explore/clover.svg" alt=""/>
                    <h4>Feed</h4>
                </a>
                <!--item-->
                <a href="/navbar-v1-profile-friends.html" class="explore-item">
                    <img src="../assets/img/icons/explore/friends.svg" alt=""/>
                    <h4>Friends</h4>
                </a>
                <!--item-->
                <a href="/navbar-v1-videos-home.html" class="explore-item">
                    <img src="../assets/img/icons/explore/videos.svg" alt=""/>
                    <h4>Books</h4>
                </a>
                <!--item-->
                <a href="/navbar-v1-pages-main.html" class="explore-item">
                    <img src="../assets/img/icons/explore/tag-euro.svg" alt=""/>
                    <h4>Pages</h4>
                </a>
                <!--item-->

                <!--item-->
                <a href="/navbar-v1-groups.html" class="explore-item">
                    <img src="../assets/img/icons/explore/house.svg" alt=""/>
                    <h4>Interests</h4>
                </a>
                <!--item-->
                <a href="/navbar-v1-stories-main.html" class="explore-item">
                    <img src="../assets/img/icons/explore/chrono.svg" alt=""/>
                    <h4>Stories</h4>
                </a>
                <!--item-->
                <a href="/navbar-v1-questions-home.html" class="explore-item">
                    <img src="../assets/img/icons/explore/question.svg" alt=""/>
                    <h4>Questions</h4>
                </a>
                <a href="/navbar-v1-groups.html" class="explore-item">
                    <img src="../assets/img/icons/explore/cake.svg" alt=""/>
                    <h4>Groups</h4>
                </a>
                <a href="/navbar-v1-settings.html" class="explore-item">
                    <img src="../assets/img/icons/explore/settings.svg" alt=""/>
                    <h4>Settings</h4>
                </a>
            </div>
        </div>
    </div>
</div>

@include('components.include.chat-modal')

<div id="end-tour-modal" class="modal end-tour-modal is-xsmall has-light-bg">
    <div class="modal-background"></div>
    <div class="modal-content">
        <div class="card">
            <div class="card-heading">
                <h3></h3>
                <!-- Close X button -->
                <div class="close-wrap">
                        <span class="close-modal">
                            <i data-feather="x"></i>
                        </span>
                </div>
            </div>
            <div class="card-body has-text-centered">
                <div class="image-wrap">
                    <img src="../assets/img/vector/logo/friendkit.svg" alt=""/>
                </div>

                <h3>That's all folks!</h3>
                <p>
                    Thanks for taking the friendkit tour. There are still tons of other
                    features for you to discover!
                </p>

                <div class="action">
                    <a href="/#demos-section" class="button is-solid accent-button raised is-fullwidth">Explore</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Concatenated js plugins and jQuery -->
<script src="{{asset('assets/js/app.js')}}"></script>
{{--<script src="https://js.stripe.com/v3/"></script>--}}
<script src="{{asset('assets/data/tipuedrop_content.js')}}"></script>

<!-- Core js -->
<script src="{{asset('assets/js/global.js')}}"></script>

<!-- Navigation options js -->
<script src="{{asset('assets/js/navbar-v1.js')}}"></script>
<script src="{{asset('assets/js/navbar-v2.js')}}"></script>
<script src="{{asset('assets/js/navbar-mobile.js')}}"></script>
<script src="{{asset('assets/js/navbar-options.js')}}"></script>
<script src="{{asset('assets/js/sidebar-v1.js')}}"></script>

<!-- Core instance js -->
<script src="{{asset('assets/js/main.js')}}"></script>
<script src="{{asset('assets/js/chat.js')}}"></script>
<script src="{{asset('assets/js/touch.js')}}"></script>
<script src="{{asset('assets/js/tour.js')}}"></script>

<!-- Components js -->
<script src="{{asset('assets/js/explorer.js')}}"></script>
<script src="{{asset('assets/js/widgets.js')}}"></script>
{{--<script src="{{asset('assets/js/modal-uploader.js')}}"></script>--}}
<script src="{{asset('assets/js/popovers-users.js')}}"></script>
<script src="{{asset('assets/js/popovers-pages.js')}}"></script>
<script src="{{asset('assets/js/lightbox.js')}}"></script>

<!-- Landing page js -->

<!-- Signup page js -->

<!-- Feed pages js -->
<script src="{{asset('assets/js/feed.js')}}"></script>

<script src="{{asset('assets/js/webcam.js')}}"></script>
<script src="{{asset('assets/js/compose.js')}}"></script>
<script src="{{asset('assets/js/autocompletes.js')}}"></script>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{asset('js/validate.min.js')}}"></script>
<script src="{{asset('js/moment-with-locales.js')}}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

{{$jscode ?? ''}}

</body>
</html>
