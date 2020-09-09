<!doctype html>
<html lang="{{ Voyager::getLocale() }}" locales="{{ implode(',', Voyager::getLocales()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ Str::finish(route('voyager.dashboard'), '/') }}">

    <title>{{ __('voyager::auth.login') }} - {{ Voyager::setting('admin.title', 'Voyager II') }}</title>
    <link href="{{ Voyager::assetUrl('css/voyager.css') }}" rel="stylesheet">
    @foreach (resolve(\Voyager\Admin\Manager\Plugins::class)->getAllPlugins() as $plugin)
        @if ($plugin instanceof \Voyager\Admin\Contracts\Plugins\Features\Provider\CSS)
            <link href="{{ Voyager::assetUrl('plugin/'.Str::slug($plugin->name).'.css') }}" rel="stylesheet">
        @endif
    @endforeach
</head>

<body>
    <div class="login sm:px-6 lg:px-8" id="voyager-login"></div>
</body>
<script src="{{ Voyager::assetUrl('js/voyager.js') }}"></script>
<script>
createVoyager({
    routes: {!! Voyager::getRoutes() !!},
    localization: {!! Voyager::getLocalization() !!},
    locales: ["{!! implode('","', Voyager::getLocales()) !!}"],
    locale: '{{ Voyager::getLocale() }}',
    initial_locale: '{{ Voyager::getLocale() }}',
    csrf_token: '{{ csrf_token() }}',
    welcome: '{{ Voyager::setting('admin.welcome', __('voyager::generic.welcome_to_voyager')) }}',
    has_login_view: {{ (Voyager::auth()->loginView() !== null ? 'true' : 'false') }},
    has_password_view: {{ (Voyager::auth()->forgotPasswordView() !== null ? 'true' : 'false') }},
}, false);
</script>
@foreach (resolve(\Voyager\Admin\Manager\Plugins::class)->getAllPlugins() as $plugin)
    @if ($plugin instanceof \Voyager\Admin\Contracts\Plugins\Features\Provider\JS)
        <script src="{{ Voyager::assetUrl('plugin/'.Str::slug($plugin->name).'.js') }}" type="text/javascript"></script>
    @endif
@endforeach
<script>
mountVoyager('#voyager-login');
</script>
@yield('js')
</html>
