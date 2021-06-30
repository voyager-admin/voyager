<template>
    <div class="login sm:px-6 lg:px-8">
        <div class="header sm:mx-auto sm:w-full sm:max-w-md">
            <div class="justify-center flex text-center">
                <icon icon="helm" :size="16" class="icon" />
            </div>
            <p class="mt-6 text-center text-sm leading-5 max-w">
                {{ welcome }}
            </p>
            <h2 class="mt-2 text-center text-3xl leading-9 font-extrabold">
                {{ __('voyager::auth.sign_in_to_account') }}
            </h2>
        </div>

        <div class="mt-8 mx-2 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="form py-8 px-4 shadow rounded-lg px-10">
                <alert v-if="false" color="red" role="alert">
                    <ul>
                        <li v-if="loginForm.hasErrors" v-for="(error, i) in loginForm.errors" :key="`login-error-${i}`">
                            {{ error }}
                        </li>
                        <li v-if="passwordForm.hasErrors" v-for="(error, i) in passwordForm.errors" :key="`password-error-${i}`">
                            {{ error }}
                        </li>
                    </ul>
                </alert>
                <div class="mb-4" v-if="false"></div>
                <!-- Built-in login view -->
                <form method="post" :action="route('voyager.login')" v-if="!passwordForgotOpen" key="login-form">
                    <input type="hidden" name="_token" :value="$store.csrfToken" />
                    <slot name="login">
                        <div class="w-full mt-1">
                            <label for="email" class="label">{{ __('voyager::auth.email') }}</label>
                            <div class="mt-1 rounded-md shadow-sm">
                                <input type="email" name="email" id="email" class="input w-full mb-4 placeholder-gray-400" autofocus>
                            </div>
                        </div>
                        <div class="w-full mt-6">
                            <label for="password" class="label">{{ __('voyager::auth.password') }}</label>
                            <div class="mt-1 rounded-md shadow-sm">
                                <input type="password" name="password" id="password" class="input w-full mb-3 placeholder-gray-400">
                            </div>
                        </div>
                        <div class="w-full flex justify-between mt-4">
                            <div class="select-none">
                                <input type="checkbox" class="input" name="remember" id="remember">
                                <label for="remember" class="text-sm leading-8 mx-1">{{ __('voyager::auth.remember_me') }}</label>
                            </div>
                            
                            <a href="#" v-if="has_password_view" class="font-medium text-sm leading-8" @click.prevent="passwordForgotOpen = true">
                                {{ __('voyager::auth.forgot_password') }}
                            </a>
                        </div>
                    </slot>

                    <div class="flex items-center justify-between mt-4">
                        <button class="button large accent w-full justify-center" type="submit">
                            {{ __('voyager::auth.login') }}
                        </button>
                    </div>
                </form>
                <form method="post" :action="route('voyager.forgot_password')" v-if="has_password_view && passwordForgotOpen" key="password-form">
                    <p>{{ __('voyager::auth.forgot_password_info') }}</p>
                    <div class="mt-4">
                        <!-- Built-in forgot password view -->
                        <input type="email" name="email" id="forgot-password-email" class="input w-full mb-4" :placeholder="__('voyager::auth.email')" autofocus>
                        <!-- End built-in forgot password view -->
                        <div class="flex items-center justify-between mt-6">
                            <button class="button accent justify-center" type="submit">
                                {{ __('voyager::auth.request_password') }}
                            </button>
                            <a href="#" @click.prevent="passwordForgotOpen = false">
                                {{ __('voyager::generic.cancel') }}
                            </a>
                        </div>
                    </div>
                </form>
                <!-- End built-in login view -->
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: [
        'welcome',
        'has_password_view'
    ],
    data() {
        return {
            passwordForgotOpen: false,
        };
    }
};
</script>
<style lang="scss">
@import "@sassmixins/bg-color";
@import "@sassmixins/text-color";

.dark .login {
    @include bg-color(login-bg-color-dark, 'colors.gray.900');
    .header {
        h2 {
            @include text-color(login-heading-color-dark, 'colors.gray.300');
        }
        p {
            @include text-color(login-text-color-dark, 'colors.gray.200');
        }
        .icon {
            @include text-color(login-icon-color-dark, 'colors.gray.200');
        }
    }

    .form {
        @include bg-color(login-form-bg-color, 'colors.gray.800');
    }
}

.login {
    @include bg-color(login-bg-color, 'colors.gray.100');
    @apply h-screen flex-col justify-center py-12;
    .header {
        h2 {
            @include text-color(login-heading-color, 'colors.gray.900');
        }
        p {
            @include text-color(login-text-color, 'colors.gray.600');
        }
        .icon {
            @include text-color(login-icon-color, 'colors.black');
        }
    }

    .form {
        @include bg-color(login-form-bg-color, 'colors.gray.50');
    }
}
</style>