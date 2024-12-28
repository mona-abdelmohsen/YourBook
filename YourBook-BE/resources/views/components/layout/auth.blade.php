<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />

    <title>{{env('APP_NAME')}} | </title>
    <link rel="icon" type="image/svg" href="{{asset('assets/img/navbar/logo.svg')}}"/>

    <!-- Google Tag Manager -->
    <script>
        ;
        (function(w, d, s, l, i) {
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
    <link href="https://fonts.googleapis.com/css?family=Montserrat:600,700,800,900" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500" rel="stylesheet" />
    <!-- Core CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/app.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/core.css')}}" />

    @yield('head')
</head>


<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KQHJPZP" height="0" width="0" style="display: none; visibility: hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <!-- Pageloader -->
    <div class="pageloader"></div>
    <div class="infraloader is-active"></div>

    @yield('content')


    @livewireScripts
    <!-- Concatenated js plugins and jQuery -->
    <script src="{{asset('assets/js/app.js')}}"></script>
    <script src="https://js.stripe.com/v3/"></script>
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
    <script src="{{asset('assets/js/modal-uploader.js')}}"></script>
    <script src="{{asset('assets/js/popovers-users.js')}}"></script>
    <script src="{{asset('assets/js/popovers-pages.js')}}"></script>
    <script src="{{asset('assets/js/lightbox.js')}}"></script>

    @yield('jscode')

</body>
</html>
