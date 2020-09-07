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
            <link href="{{ $plugin->provideCSS() }}" rel="stylesheet">
        @endif
    @endforeach
</head>

<body>
    <div class="login sm:px-6 lg:px-8" id="voyager-login">
        <div class="header sm:mx-auto sm:w-full sm:max-w-md">
            <div class="justify-center flex text-center">
                <icon icon="helm" size="16" class="icon"></icon>
            </div>
            <p class="mt-6 text-center text-sm leading-5 max-w">
                {{ Voyager::setting('admin.welcome', __('voyager::generic.welcome_to_voyager')) }}
            </p>
            <h2 class="mt-2 text-center text-3xl leading-9 font-extrabold">
                {{ __('voyager::auth.sign_in_to_account') }}
            </h2>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="form py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <login
                    error="{{ Session::get('error', null) }}"
                    success="{{ Session::get('success', null) }}"
                    :old="{{ json_encode(old()) }}"
                    :has-password-forgot="{{ Voyager::auth()->forgotPasswordView() === null ? 'false' : 'true' }}"
                >
                    @if (Voyager::auth()->loginView())
                    <template v-slot:login>
                        {!! Voyager::auth()->loginView() !!}
                    </template>
                    @endif
                    @if (Voyager::auth()->forgotPasswordView())
                    <template v-slot:forgot_password>
                        {!! Voyager::auth()->forgotPasswordView() !!}
                    </template>
                    @endif
                </login>
            </div>
        </div>
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
    debug: {{ var_export(config('app.debug') ?? false, true) }},
    jsonOutput: {{ var_export(Voyager::setting('admin.json-output', true)) }},
    csrf_token: '{{ csrf_token() }}',
});
</script>
@foreach (resolve(\Voyager\Admin\Manager\Plugins::class)->getAllPlugins() as $plugin)
    @if ($plugin instanceof \Voyager\Admin\Contracts\Plugins\Features\Provider\JS)
        <script src="{{ $plugin->provideJS() }}" type="text/javascript"></script>
    @endif
@endforeach
<script>
mountVoyager('#voyager-login');
</script>
@yield('js')
</html>
