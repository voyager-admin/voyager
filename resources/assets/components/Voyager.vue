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

        document.addEventListener('keyup', function (e) {
            if (e.ctrlKey) {
                if (e.key === 's') {
                    if ($eventbus.hasListener('ctrl-s-combo')) {
                        e.preventDefault();
                        e.stopPropagation();
                        $eventbus.emit('ctrl-s-combo');
                    }
                } else if (e.code == 'ArrowRight' || e.code == 'ArrowUp') {
                    vm.$store.nextLocale();
                } else if (e.code == 'ArrowLeft' || e.code == 'ArrowDown') {
                    vm.$store.previousLocale();
                }
            }
        });

        // Toggle darkmode when cookie is set or dark-mode is the prefered color scheme
        var dark_mode = vm.getCookie('dark-mode');
        if (window.matchMedia && dark_mode == '') {
            var dark = window.matchMedia('(prefers-color-scheme: dark)');
            if (dark.matches) {
                vm.$store.toggleDarkMode(true);
            }
            // TODO: Once Safari follow the specs, this should be .dark.addEventListener('change', () => {})
            dark.addListener(function (e) {
                vm.$store.toggleDarkMode(e.matches);
            });
        }
        if (dark_mode == 'true') {
            vm.$store.toggleDarkMode(true);
        }

        // Toggle sidebar when cookie is set
        var sidebar_open = vm.getCookie('sidebar-open');
        if (sidebar_open == 'false') {
            vm.$store.closeSidebar();
        }

        $eventbus.on('darkmode', function (darkmode) {
            vm.setCookie('dark-mode', darkmode);
        });
        $eventbus.on('sidebar-open', function (open) {
            vm.setCookie('sidebar-open', open);
        });
    },
}
</script>