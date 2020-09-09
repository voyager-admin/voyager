<!doctype html>
<html lang="{{ Voyager::getLocale() }}" locales="{{ implode(',', Voyager::getLocales()) }}" dir="{{ __('voyager::generic.is_rtl') == 'true' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ Str::finish(route('voyager.dashboard'), '/') }}">
    <meta name="asset-url" content="{{ Str::finish(asset(''), '/') }}">

    <title>{{ $title ?? '' }} - {{ Voyager::setting('admin.title', 'Voyager II') }}</title>
    <link href="{{ Voyager::assetUrl('css/voyager.css') }}" rel="stylesheet">
    @foreach (resolve(\Voyager\Admin\Manager\Plugins::class)->getAllPlugins() as $plugin)
        @if ($plugin instanceof \Voyager\Admin\Contracts\Plugins\Features\Provider\CSS)
            <link href="{{ $plugin->provideCSS() }}" rel="stylesheet">
        @endif
    @endforeach
</head>

<body>
    <div id="voyager"></div>
</body>
<script src="{{ Voyager::assetUrl('js/voyager.js') }}"></script>
<script>
createVoyager({
    routes: {!! Voyager::getRoutes() !!},
    localization: {!! Voyager::getLocalization() !!},
    locales: ["{!! implode('","', Voyager::getLocales()) !!}"],
    locale: '{{ Voyager::getLocale() }}',
    initial_locale: '{{ Voyager::getLocale() }}',
    breads: {!! json_encode(resolve(\Voyager\Admin\Manager\Breads::class)->getBreads()) !!},
    formfields: {!! json_encode(resolve(\Voyager\Admin\Manager\Breads::class)->getFormfields()) !!},
    debug: {{ var_export(config('app.debug') ?? false, true) }},
    jsonOutput: {{ var_export(Voyager::setting('admin.json-output', true)) }},
    csrf_token: '{{ csrf_token() }}',
    searchPlaceholder: '{{ resolve(\Voyager\Admin\Manager\Breads::class)->getBreadSearchPlaceholder() }}',
    current_url: '{{ Str::finish(url()->current(), '/') }}',
    user: {
        name: '{{ Voyager::auth()->name() }}',
        avatar: '{{ Voyager::assetUrl('images/default-avatar.png') }}',
    },
    sidebar: {
        title: '{{ Voyager::setting('admin.sidebar-title', 'Voyager II') }}',
        items: {!! resolve(\Voyager\Admin\Manager\Menu::class)->getItems(resolve(\Voyager\Admin\Manager\Plugins::class)) !!},
    },
    page: {
        component: '{{ $component }}',
        title: '{{ $title ?? '' }}',
        parameters: {!! json_encode($parameters) !!},
    },
});
</script>
@foreach (resolve(\Voyager\Admin\Manager\Plugins::class)->getAllPlugins() as $plugin)
    @if ($plugin instanceof \Voyager\Admin\Contracts\Plugins\Features\Provider\JS)
        <script src="{{ Voyager::assetUrl('plugin/'.Str::slug($plugin->name).'.js') }}" type="text/javascript"></script>
    @endif
@endforeach
<script>
mountVoyager();
</script>
@yield('js')
</html>
