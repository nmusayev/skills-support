<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Meta Information -->
    <meta name="description" content="Skills-Support: Improve your skills, Help community!">
    <meta name="keywords" content="skills, support, improve, ask, help, community, people">
    <meta name="author" content="Nadir Musayev">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Content Sharing -->
    <meta property="og:title" content="Improve your skills, Help community!">
    <meta property="og:site_name" content="skills-support">
    <meta property="og:url" content="https://skills-support.com">
    <meta property="og:description" content="Improve your skills, Help community!">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ URL::asset('images/LOGO.png') }}">

    <link rel="icon" href="{{ URL::asset('images/LOGO.png') }}">

    <title>Skills-Support: Improve your skills, Help community!</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="facade">
<div id="app">
    <nav class="fixed-top navbar navbar-expand-md navbar-light shadow-sm">
        <div class="container">
            @auth
                <a class="navbar-brand" href="{{ url('/profile') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
            @endauth
            @guest
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
            @endguest
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

                </ul>

                <div class="navbar-nav ml-auto">
                    <form id="search-form" action="/search-results/" method="get" class="form-inline">
                        <input id="search-input" class="form-control" type="text"
                               name="search-key" placeholder="{{ __('site.label.search_here') }}">

                        <a
                            href="/search-results/"
                            id="search-btn"
                            class="d-none btn btn-success">
                            Search
                        </a>
                    </form>
                </div>

            <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a exact-active-class="active" href="/users" class="nav-link">
                                {{ __('site.nav.users') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a exact-active-class="active" href="/community" class="nav-link">
                                {{ __('site.nav.community') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('site.nav.login') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('site.nav.register') }}</a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <div class="btn-group">
                                <button type="button" class="btn dropdown-toggle text-white" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                    <b>{{ config('app.locales')[app()->getLocale()] }}</b>
                                </button>
                                <div class="dropdown-menu languages">
                                    @foreach(config('app.locales') as $index => $name)
                                        @if($index != app()->getLocale())
                                            <a class="dropdown-item"
                                               href="{{ route('setLanguage', $index) }}">{{ $name }}</a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </li>
                    @else
                        <li class="nav-item">
                            <router-link exact-active-class="active" tag="a" to="/profile/{{ auth()->user()->id }}"
                                         class="nav-link">
                                {{ __('site.nav.profile') }}
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link exact-active-class="active" tag="a" to="/community" class="nav-link">
                                {{ __('site.nav.community') }}
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link exact-active-class="active" tag="a" to="/users" class="nav-link">
                                {{ __('site.nav.users') }}
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <div class="btn-group">
                                <button type="button" class="btn dropdown-toggle text-white" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                    <b>{{ config('app.locales')[app()->getLocale()] }}</b>
                                </button>
                                <div class="dropdown-menu languages">
                                    @foreach(config('app.locales') as $index => $name)
                                        @if($index != app()->getLocale())
                                            <a class="dropdown-item"
                                               href="{{ route('setLanguage', $index) }}">{{ $name }}</a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link text-light bell-link hasNotification" href="#" id="navbarDropdown"
                               role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bell"></i>
                                <span>{{ auth()->user()->unreadNotifications()->count() }}</span>
                            </a>
                            <ul class="dropdown-menu notifications">
                                <li class="head text-light" style="background-color: #DC143C">
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-12">
                                            <span>Notifications</span>
                                        </div>
                                    </div>
                                </li>

                                @foreach(auth()->user()->notifications as $notification)
                                    <li class="notification-box {{ isset($notification->read_at) ?: 'bg-gray' }} border border-success">
                                        <a class="d-block"
                                           href="/question-detail/{{ $notification->data['question_id'] }}"
                                           target="_blank">
                                            <div class="row pt-1 pb-1">
                                                <div class="col-lg-3 col-sm-3 col-3 text-center">
                                                    <img
                                                        src="{{ url('storage/' . \App\User::find($notification->data['user_id'])->profile_image) }}"
                                                        class="img-thumbnail"
                                                        style="object-fit: cover; width: 63px; height: 63px; border-radius: 100px;">
                                                </div>
                                                <div class="col-lg-8 col-sm-8 col-8">
                                                    <strong class="text-info">
                                                        {{ $notification->data['user_name'] }}
                                                    </strong>
                                                    <div>
                                                        {{ $notification->data['title'] }}
                                                    </div>
                                                    <small class="text-success">
                                                        {{ $notification->data['created_at'] }}
                                                    </small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach

                                @if(!auth()->user()->notifications->count())
                                    <p class="p-3 text-info">You do not have any notification yet!</p>
                                @endif

                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('site.nav.logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>

                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4 mt-5 mb-4">
        @yield('content')
    </main>

    <footer class="fixed-bottom text-center">
        <ul class="list-unstyled list-inline mb-0">
            <li class="list-inline-item">support@skills-support.com |</li>
            <li class="list-inline-item">(+994)55-834-7803 |</li>
            <li class="list-inline-item">
                <a href="https://www.facebook.com/com.skills.support"
                   class="text-white"
                   target="_blank">facebook</a> &nbsp; |
            </li>
            <li class="list-inline-item">{{ now()->year }} &copy; Skills-Support</li>
        </ul>
    </footer>
</div>

<!-- Scripts -->
<script defer>
    let auth = {
        'check': '{{ Auth::check() ?: 0 }}',
        'user': JSON.parse('{{ Auth::user() ?: '{}' }}'.replace(/&quot;/g, '\"')),
    };
</script>
<script src="{{ asset('js/app.js') }}"></script>
<script defer>
    $.ajaxSetup({
        headers: {
            'X-CSRF-Token': $('meta[name="_token"]').attr('content')
        }
    });

    $('.languages-select').select2();

    // Community skill multiselect
    // $('.skills-select').select2({
    //     ajax: {
    //         url: 'api/skill/all',
    //         dataType: 'json',
    //         "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //         data: function (params) {
    //             let query = {
    //                 search: params.term,
    //                 type: 'public'
    //             };
    //
    //             // Query parameters will be ?search=[term]&type=public
    //             return query;
    //         },
    //         processResults: function (data) {
    //             console.log(data);
    //             return {
    //                 results: data.success,
    //             };
    //         },
    //     }
    // });

    // $('.skills-select').select2({
    //     ajax: {
    //         url: 'https://api.github.com/orgs/select2/repos',
    //         data: function (params) {
    //             let query = {
    //                 search: params.term,
    //                 type: 'public'
    //             };
    //
    //             // Query parameters will be ?search=[term]&type=public
    //             return query;
    //         },
    //         processResults: function (data) {
    //             // Transforms the top-level key of the response object from 'items' to 'results'
    //             console.log(data);
    //             return {
    //                 results: data.items
    //             };
    //         }
    //     }
    // });

    // Profile setting number heights
    let width = $('.numbers').width();
    $('.numbers').height(width);

    $(".alert").delay(2000).slideUp(200, function () {
        $(this).alert('close');
    });

    {{--let window = {--}}
    {{--    'auth' : {--}}
    {{--        'login': '{{ Auth::check() ?: 0 }}',--}}
    {{--        'user': '{{ Auth::user() }}'--}}
    {{--    }--}}
    {{--}--}}


    // Search key key-up setting input value to search btn href
    $('#search-btn').on('click', function (e) {
        // Validation
        if ($('#search-input').val() === '') {
            e.preventDefault();
            alert('input is empty');
        }

        // Getting value
        let value = $('#search-input').val();

        // Appending new value to link
        let current_link = $('#search-btn').attr("href");
        $('#search-btn').attr('href', current_link + encodeURIComponent(value));

        // Clearing input for new search
        $('#search-input').val('');
    });

    // $("form").submit(function(e){
    //     e.preventDefault();
    //     $('#search-btn').trigger('click');
    // });


    // // Enter on search click to btn
    $('#search-input').keypress(function (e) {
        let key = e.which;
        if (key === 13) {
            // Validation
            if ($('#search-input').val() === '') {
                e.preventDefault();
                alert('input is empty');
            }

            // Getting value
            let value = $('#search-input').val();


            // Appending new value to link
            let current_link = $('form#search-form').attr("action");
            $('form#search-form').attr('action', current_link + encodeURIComponent(value));

            // Clearing input for new search
            $('#search-input').val('');
        }
    });
</script>

</body>
</html>
