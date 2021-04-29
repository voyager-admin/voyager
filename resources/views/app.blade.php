<!doctype html>
<html lang="{{ Voyager::getLocale() }}" locales="{{ implode(',', Voyager::getLocales()) }}" dir="{{ __('voyager::generic.is_rtl') == 'true' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ Str::finish(route('voyager.dashboard'), '/') }}">
    <meta name="description" content="{{ Voyager::setting('admin.description', 'Voyager II') }}">
    <meta http-equiv="Cache-control" content="public">

    <title>{{ $page['props']['title'] ?? '' }} - {{ Voyager::setting('admin.title', 'Voyager II') }}</title>
    @if (isset($voyagerDevServer))
        <link href="{{ $voyagerDevServer }}css/voyager.css" rel="stylesheet">
    @else
        <link href="{{ Voyager::assetUrl('css/voyager.css') }}" rel="stylesheet">
    @endif

    @php $fontProvidedByPlugin = false; @endphp

    @foreach (resolve(\Voyager\Admin\Manager\Plugins::class)->getAllPlugins() as $plugin)
        @if ($plugin instanceof \Voyager\Admin\Contracts\Plugins\Features\Provider\CSS)
            <link href="{{ Voyager::assetUrl('plugin/'.Str::slug($plugin->name).'.css') }}" rel="stylesheet">

            @if ($plugin instanceof \Voyager\Admin\Contracts\Plugins\Features\Provider\Font)
                @php $fontProvidedByPlugin = true; @endphp
            @endif
        @endif
    @endforeach

@if (!$fontProvidedByPlugin)
    <link href="{{ Voyager::assetUrl('css/font.css') }}" rel="stylesheet">
@endif
</head>

<body>
    <div id="voyager" data-page="{{ json_encode($page) }}"></div>

    @if (isset($voyagerDevServer))
    <div id="js-warning" style="display:none">
        {!! __('voyager::generic.dev_server_unavailable', ['url' => $voyagerDevServer]) !!}
    </div>
    @endif
</body>

@routes

@if (isset($voyagerDevServer))
    <script src="{{ $voyagerDevServer }}js/voyager.js"></script>
    <script src="{{ $voyagerDevServer }}js/icons.js"></script>
@else
    <script src="{{ Voyager::assetUrl('js/voyager.js') }}"></script>
    <script src="{{ Voyager::assetUrl('js/icons.js') }}"></script>
@endif

<script>
if (window.createVoyager) {
    createVoyager({
        'breads': {!! json_encode(resolve(\Voyager\Admin\Manager\Breads::class)->getBreads()->values()) !!},
        'formfields': {!! json_encode(resolve(\Voyager\Admin\Manager\Breads::class)->getFormfields()) !!},
        'localization': {!! Voyager::getLocalization() !!}
    });
} else {
    @if (isset($voyagerDevServer))
    document.getElementById('js-warning').style.display = 'block';
    @endif
}

</script>
@foreach (resolve(\Voyager\Admin\Manager\Plugins::class)->getAllPlugins() as $plugin)
    @if ($plugin instanceof \Voyager\Admin\Contracts\Plugins\Features\Provider\JS)
        <script src="{{ Voyager::assetUrl('plugin/'.Str::slug($plugin->name).'.js') }}" type="text/javascript"></script>
    @endif
@endforeach
<script>
if (window.mountVoyager) {
    mountVoyager();
}
</script>
@yield('js')

@if (isset($voyagerDevServer))
<style>
#js-warning {
    width: 100%;
    text-align: center;
    font-size: 1.25rem;
    padding-top: 20px;
}

#js-warning code {
    background-color: #eee;
}
</style>
@endif
</html>
