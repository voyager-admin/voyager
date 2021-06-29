<!doctype html>
<html lang="{{ Voyager::getLocale() }}" dir="{{ __('voyager::generic.is_rtl') == 'true' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="base-url" content="{{ Str::finish(route('voyager.dashboard'), '/') }}">
    @if ($devServerWanted && $devServerAvailable)
        <meta name="asset-url" content="{{ $devServerUrl }}">
    @else
        <meta name="asset-url" content="{{ Voyager::assetUrl('') }}">
    @endif
    
    <meta name="description" content="{{ Voyager::setting('admin.description', 'Voyager II') }}">
    <meta http-equiv="Cache-control" content="public">

    <title>{{ Voyager::setting('admin.title', 'Voyager II') }}</title>
    @if ($devServerWanted && $devServerAvailable)
        <link href="{{ $devServerUrl }}css/voyager.css" rel="stylesheet">
    @else
        <link href="{{ Voyager::assetUrl('css/voyager.css') }}" rel="stylesheet">
    @endif

    @foreach (resolve(\Voyager\Admin\Manager\Plugins::class)->getAllPlugins() as $plugin)
        @if ($plugin instanceof \Voyager\Admin\Contracts\Plugins\Features\Provider\CSS)
            <link href="{{ Voyager::assetUrl('plugin/'.Str::slug($plugin->name).'.css') }}" rel="stylesheet">
        @endif
    @endforeach
</head>

<body>
    <div id="tooltips" class="h-0 w-0"></div>
    <div id="voyager" data-page="{{ json_encode($page) }}"></div>
</body>

@routes

@if ($devServerWanted && $devServerAvailable)
    <script src="{{ $devServerUrl }}js/voyager.js"></script>
@else
    <script src="{{ Voyager::assetUrl('js/voyager.js') }}"></script>
@endif

<script>
createVoyager({!! json_encode(array_merge(['title' => $title], Voyager::getViewData())) !!});
</script>

@foreach (resolve(\Voyager\Admin\Manager\Plugins::class)->getAllPlugins() as $plugin)
    @if ($plugin instanceof \Voyager\Admin\Contracts\Plugins\Features\Provider\JS)
        <script src="{{ Voyager::assetUrl('plugin/'.Str::slug($plugin->name).'.js') }}"></script>
    @endif
@endforeach
<script>
mountVoyager();
</script>
@yield('js')
</html>
