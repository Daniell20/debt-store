<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Debt Store</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
        html,
        body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            margin: 0;
        }

        body {
            overflow-x: hidden;
            /* Hide horizontal overflow */
        }

        .full-height {
            min-height: 100vh;
            /* Ensure full height even on small content */
            display: flex;
            flex-direction: column;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title img {
            max-width: 100%;
            /* Make the image responsive */
            height: auto;
            /* Maintain aspect ratio */
        }

        .links>a {
            color: #636b6f;
            padding: 0 10px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <div class="flex-center position-ref full-height">
        @if (Route::has('login'))
            <div class="top-right links">
                @if (Auth::check())
                    <a href="{{ url('/home') }}">Home</a>
                @else
                    <a href="{{ url('/login') }}">Login</a>
                    {{-- <a href="{{ url('/register') }}">Register</a> --}}
                @endif
            </div>
        @endif

        <div class="content">
            <div class="title m-b-md">
                <img src="{{ asset('images/logos/debt store app.jpg') }}" alt="">
            </div>
        </div>
    </div>
</body>

</html>
