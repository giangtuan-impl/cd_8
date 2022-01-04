<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{asset('assets/css/vendors/bootstrap.min.css')}}">
    {{-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">  --}}
    <style>
        body, * {
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 960px;
            margin: auto;
            margin-top: 170px;
        }

        .title {
            font-family: Arial, Helvetica, sans-serif;
            color: #525252;
            text-shadow: 0px 3px 6px #00000029;
            font-size: 3.25rem;
            margin-top: 0;
            text-align: center;
            margin-bottom: 150px;
            font-weight: 700;
        }

        .download-icon {
            margin: 0 auto 15px;
            display: block;
        }

        .download-box {
            text-align: center;

        }

        .download-link {
            width: 239px;
            height: 64px;
            font-size: 20px;
            color: #FFFFFF;
            border-radius: 34px;
            background-color: #EA9D43;
            box-shadow: 0px 3px 6px #00000029;
            border: none;
            outline: none;
            display: inline-block;
            padding: 17px;
        }

        .download-link:hover {
            background-color: #e18d2a;
            color: #fff;
            text-decoration: none;
        }

        .download-version {
            margin-bottom: 25px;
        }

        @media (max-width: 767px) {
            .container {
                margin-top: 50px;
            }
            .title {
                margin-bottom: 50px;
            }
            .download-icon {
                margin-bottom: 20px;
            }

            .download-box:first-of-type {
                margin-bottom: 40px;
            }
        }
    </style>
    <script src="{{asset('assets/js/jquery-3.5.1.min.js')}}"></script>
    <script>
        function getMobileOperatingSystem() {
           var userAgent = navigator.userAgent || navigator.vendor || window.opera;

           // Windows Phone must come first because its UA also contains "Android"
           if (/windows phone/i.test(userAgent)) {
               return "Windows Phone";
           }

           if (/android/i.test(userAgent)) {
               return "Android";
           }

           // iOS detection from: http://stackoverflow.com/a/9039885/177710
           if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
               return "IOS";
           }

           return "unknown";
       }

       $(document).ready(function () {
           const device = getMobileOperatingSystem();
           switch (device) {
               case "IOS":
                   $(".IOSApp").show();
                   $(".AndroidApp").hide();
                   break;
               case "Android":
                   $(".AndroidApp").show();
                   $(".IOSApp").hide();
                   break;
               default:
                   $(".AndroidApp").show();
                   $(".IOSApp").show();
                   break;
           }
       });
   </script>
</head>
<body>
    <div class="container">
        <h2 class="title">{{ request('name') }} Application</h2>
        <div class="row">
            <div class="col-md-6 download-box IOSApp" style="display: none">
                <img class="download-icon" src="{{ url('assets/image/iOS.svg') }}" alt="">
                @if(isset($iosBuild->id))
                    <p class="download-version">Version: {{ $iosBuild->version_number }}<br/>Build number: {{ $iosBuild->build_number }}</p>
                    <a href="{{ config('constants.IOS_PARAMS.PLIST_DOWNLOAD_LINK') . route('build.plist-download', ['id' => $iosBuild->id]) }}"  target="_blank" class="download-link">Install</a>
                @endif
            </div>
            <div class="col-md-6 download-box AndroidApp" style="display: none">
                <img class="download-icon" src="{{ url('assets/image/Android.svg') }}" alt="">
                @if(isset($androidBuild->id))
                    <p class="download-version">Version: {{ $androidBuild->version_number }}<br/>Build number: {{ $androidBuild->build_number }}</p>
                    <a href="{{ route('build.download', ['id' => $androidBuild->id]) }}" target="_blank" class="download-link">Install</a>
                @endif
            </div>
        </div>
    </div>
</body>

</html>
