<template>
    <slide-left-transition class="h-screen flex overflow-hidden" tag="div" group>
        <div key="loader">
            <fade-transition :duration="500">
                <div class="loader" v-if="$store.pageLoading">
                    <icon icon="helm" size="auto" class="block icon animate-spin-slow"></icon>
                </div>
            </fade-transition>
        </div>
        <sidebar></sidebar>
        <div class="flex flex-col w-0 flex-1 overflow-hidden" key="content">
            <main class="flex-1 relative z-0 overflow-y-auto pt-2 pb-6 outline-none">
                <span id="top"></span>
                <navbar></navbar>
                <div class="mx-auto sm:px-3 md:px-4" id="top">
                    <!-- Content -->
                    <component :is="$store.page.component" v-bind="$store.page.parameters"></component>
                    <!-- End content -->
                </div>
            </main>
        </div>
        <notifications key="notifications"></notifications>
    </slide-left-transition>
</template>

<script>
import Sidebar from './Sidebar';
import Navbar from './Navbar';

export default {
    components: {
        'sidebar': Sidebar,
        'navbar': Navbar,
    },
    props: ['routes', 'localization', 'locales', 'locale', 'initial_locale', 'breads', 'formfields', 'debug', 'jsonOutput', 'csrf_token', 'searchPlaceholder', 'current_url', 'user', 'sidebar', 'page'],
    created: function () {
        var vm = this;

        for (const key in vm.$props) {
            if (vm.$store.hasOwnProperty(key)) {
                vm.$store[key] = vm.$props[key];
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            vm.$store.pageLoading = false;
        });
    },
}
</script>