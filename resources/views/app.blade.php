<!doctype html>
<html lang="{{ Voyager::getLocale() }}" locales="{{ implode(',', Voyager::getLocales()) }}" dir="{{ __('voyager::generic.is_rtl') == 'true' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ Str::finish(route('voyager.dashboard'), '/') }}">
    <meta name="asset-url" content="{{ Str::finish(asset(''), '/') }}">

    <title>@yield('page-title') - {{ Voyager::setting('admin.title', 'Voyager II') }}</title>
    <link href="{{ Voyager::assetUrl('css/voyager.css') }}" rel="stylesheet">
    @foreach (resolve(\Voyager\Admin\Manager\Plugins::class)->getAllPlugins() as $plugin)
        @if ($plugin instanceof \Voyager\Admin\Contracts\Plugins\Features\Provider\CSS)
            <link href="{{ $plugin->provideCSS() }}" rel="stylesheet">
        @endif
    @endforeach
</head>

<body>
    <div was="slide-x-left-transition" class="h-screen flex overflow-hidden" id="voyager" tag="div" group>
        <div key="loader">
            <div was="fade-transition" :duration="500">
                <div class="loader" v-if="$store.pageLoading">
                    <icon icon="helm" size="auto" class="block icon animate-spin-slow"></icon>
                </div>
            </div>
        </div>
        @include('voyager::sidebar')
        <div class="flex flex-col w-0 flex-1 overflow-hidden" key="content">
            <main class="flex-1 relative z-0 overflow-y-auto pt-2 pb-6 outline-none">
                <span id="top"></span>
                @include('voyager::navbar')
                <div class="mx-auto sm:px-3 md:px-4" id="top">
                    @yield('content')
                </div>
            </main>
        </div>
        <notifications key="notifications"></notifications>
    </div>
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
    menuItems: {!! resolve(\Voyager\Admin\Manager\Menu::class)->getItems(resolve(\Voyager\Admin\Manager\Plugins::class)) !!},
});
</script>
@foreach (resolve(\Voyager\Admin\Manager\Plugins::class)->getAllPlugins() as $plugin)
    @if ($plugin instanceof \Voyager\Admin\Contracts\Plugins\Features\Provider\JS)
        <script src="{{ $plugin->provideJS() }}" type="text/javascript"></script>
    @endif
@endforeach
<script>
mountVoyager();
</script>
@yield('js')
</html>
