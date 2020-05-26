<!doctype html>
<html lang="{{ Voyager::getLocale() }}" locales="{{ implode(',', Voyager::getLocales()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ Str::finish(route('voyager.dashboard'), '/') }}">

    <title>{{ __('voyager::auth.login') }} - {{ Voyager::setting('admin.title', 'Voyager II') }}</title>
    <link href="{{ Voyager::assetUrl('css/voyager.css') }}" rel="stylesheet">
    @foreach (resolve(\Voyager\Admin\Manager\Plugins::class)->getPluginsByType('theme')->where('enabled') as $theme)
        <link href="{{ $theme->getStyleRoute() }}" rel="stylesheet">
    @endforeach
</head>

<body>

    <div class="h-screen bg-gray-50 dark:bg-gray-900 flex flex-col justify-center py-12 sm:px-6 lg:px-8" id="voyager-login">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="justify-center flex text-center">
                <icon icon="helm" size="16" class="text-black dark:text-gray-200"></icon>
            </div>
            <p class="mt-6 text-center text-sm leading-5 text-gray-600 dark:text-gray-200 max-w">
                Welcome to Voyager
            </p>
            <h2 class="mt-2 text-center text-3xl leading-9 font-extrabold text-gray-900 dark:text-gray-300">
                Sign in to your account
            </h2>
            
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10">

                <login error="{{ Session::get('error', null) }}" success="{{ Session::get('success', null) }}" :old="{{ json_encode(old()) }}">
                    @if (Voyager::auth()->loginView())
                    <div slot="login">
                        {!! Voyager::auth()->loginView() !!}
                    </div>
                    @endif

                    @if (Voyager::auth()->forgotPasswordView())
                    <div slot="forgot_password">
                        {!! Voyager::auth()->forgotPasswordView() !!}
                    </div>
                    @endif
                </login>

            </div>
        </div>
    </div>

</body>
<script src="{{ Voyager::assetUrl('js/voyager.js') }}"></script>
<script>
var voyager = new Vue({
    el: '#voyager-login',
    created: function () {
        this.$language.localization = {!! Voyager::getLocalization() !!};
        this.$store.routes = {!! Voyager::getRoutes() !!};
        this.$store.debug = {{ var_export(config('app.debug') ?? false, true) }};

        var dark_mode = this.getCookie('dark-mode');
        if (dark_mode == 'true') {
            this.$store.toggleDarkMode();
        }
    },
});
</script>
@yield('js')
</html>