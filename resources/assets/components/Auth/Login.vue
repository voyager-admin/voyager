<template>
<div>
    <div was="fade-transition" :duration="150" tag="div" group>
        <form method="post" :action="route('voyager.login')" v-if="!passwordForgotOpen" key="login-form">
            <input type="hidden" name="_token" :value="$store.csrf_token">

            <alert v-if="error" color="red" role="alert" :closebutton="false">
                {{ error }}
            </alert>
            <alert v-if="success" color="green" role="alert" :closebutton="false">
                {{ success }}
            </alert>
            <div class="mb-4" v-if="success || error"></div>

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
                    <div>
                        <input type="checkbox" class="input" name="remember" id="remember">
                        <label for="remember" class="text-sm leading-8">{{ __('voyager::auth.remember_me') }}</label>
                    </div>
                    
                    <a href="#" v-if="hasPasswordForgot" class="font-medium text-sm leading-8" @click.prevent="passwordForgotOpen = true">
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
        <form method="post" :action="route('voyager.forgot_password')" v-if="hasPasswordForgot && passwordForgotOpen" key="password-form">
            <h2 class="mb-6 font-bold">{{ __('voyager::auth.forgot_password') }}</h2>
            <input type="hidden" name="_token" :value="$store.csrf_token">
            <div class="mt-4">
                <slot name="forgot_password" />
                <div class="flex items-center justify-between mt-6">
                    <button class="button accent justify-center" type="submit">
                        {{ __('voyager::auth.request_password') }}
                    </button>
                    <a href="#" @click.prevent="passwordForgotOpen = false">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
</template>
<script>
export default {
    props: ['error', 'success', 'old'],
    data: function () {
        return {
            passwordForgotOpen: false,
        };
    },
    computed: {
        hasPasswordForgot: function () {
            return this.$slots['forgot_password'] !== undefined;
        }
    },
};
</script>
<style lang="scss" scoped>
.dark .login-form {
    h2 {
        @apply text-gray-200;
    }
}

.login-form {
    h2 {
        @apply text-gray-800;
    }
}
</style>