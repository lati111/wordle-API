<html>
    <head>
        <title>jarnovanzelst.nl | @yield('htmlTitle')</title>
        @vite('resources/css/app.css')
    </head>
    <body>
        <header>
            @isset($username)
                <div id="userContainer" class="absolute top-2 right-3 text-right">
                    <div class="text-lg">{{$username}}</div>
                    <a href="{{route("logout")}}"><button class="interactive">Sign Out</button></a>
                </div>
            @endisset
            <div id="banner" class="bg-primary py-7">
                <div id="siteTitle" class="text-center font-bold text-5xl uppercase title">
                    <span>jarno</span>
                    <span class="text-sub">vanzelst</span>
                    <span>.nl</span>
                </div>
                <div id="bannerTitle" class="text-center font-medium text-3xl uppercase title">@yield('title')</div>
                <ul id="nav" class="flex place-content-center py-3">
                    <li class="mr-3">
                        <a href="{{ route('home.show') }}">
                            <span class="font-semibold text-lg">Home</span>
                        </a>
                    </li>
                    <li class="mr-3">
                        <a href="{{ route('portofolio.show') }}">
                            <span class="font-semibold text-lg">Portofolio</span>
                        </a>
                    </li>
                    <li class="mr-3">
                        <a href="{{ route('forum.show') }}">
                            <span class="font-semibold text-lg">Forum</span>
                        </a>
                    </li>
                </ul>
            </div>
        </header>
        <div>
            @yield('content')
        </div>
    </body>
    @vite('resources/js/app.js')
    <script>
        @yield('script')
    </script>
</html>
