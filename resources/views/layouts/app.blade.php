<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{asset('assets/css/vendors/bootstrap.min.css')}}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/vendors/toastr.min.css')}}">
    @yield('style')
</head>
<body>
    @include('layouts.header')

    @yield('content')

    <script src="{{asset('assets/js/jquery-3.5.1.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
    {{-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> --}}
    <script src="{{ asset('assets/js/sweetalert.min.js')}}"></script>
    <script src="{{ asset('assets/js/axios.min.js')}}"></script>
    <script src="{{ asset('assets/js/scripts.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/toastr.min.js')}}"></script>
    <script>
        $('.backButton').on('click', function() {
            window.history.back();
        });
    </script>
    <script>
        toastr.options.progressBar = true;
        @if(Session::has('message'))
            var type="{{Session::get('alert-type','info')}}"

            switch(type){
                case 'info':
                    toastr.info("{{ Session::get('message') }}");
                    break;
                case 'success':
                    toastr.success("{{ Session::get('message') }}");
                    break;
                case 'warning':
                    toastr.warning("{{ Session::get('message') }}");
                    break;
                case 'error':
                    toastr.error("{{ Session::get('message') }}");
                    break;
            }
        @endif
    </script>

    @yield('scripts')
</body>
</html>
