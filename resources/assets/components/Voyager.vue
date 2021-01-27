<template>
    <div class="h-screen flex overflow-hidden">
        <div>
            <fade-transition tag="div" :duration="500">
                <div class="loader" v-if="$store.pageLoading">
                    <icon icon="helm" size="auto" class="block icon animate-spin-slow"></icon>
                </div>
            </fade-transition>
        </div>
        <sidebar />
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <main class="flex-1 relative z-0 overflow-y-auto pt-2 pb-6 outline-none" id="content">
                <span id="top"></span>
                <navbar></navbar>
                <div class="mx-auto sm:px-3 md:px-4" id="top">
                    <!-- Content -->
                    <component :is="$store.page.component" v-bind="$store.page.parameters"></component>
                    <!-- End content -->
                </div>
            </main>
        </div>
        <notifications />
    </div>
</template>

<script>
import Sidebar from './Sidebar';
import Navbar from './Navbar';
import Notifications from './UI/Notifications';

export default {
    components: {
        'sidebar': Sidebar,
        'navbar': Navbar,
        'notifications': Notifications
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

        document.addEventListener('keydown', function (e) {
            if (e.ctrlKey) {
                if (e.key === 's') {
                    if ($eventbus.hasListener('ctrl-s-combo')) {
                        $eventbus.emit('ctrl-s-combo', e);
                        e.preventDefault();
                    }
                } else if (e.code == 'ArrowRight' || e.code == 'ArrowUp') {
                    vm.$store.nextLocale();
                } else if (e.code == 'ArrowLeft' || e.code == 'ArrowDown') {
                    vm.$store.previousLocale();
                }
            }
        });

        vm.$store.initDarkMode();

        // Toggle sidebar when cookie is set
        var sidebar_open = vm.getCookie('sidebar-open');
        if (sidebar_open == 'false') {
            vm.$store.closeSidebar();
        }
        $eventbus.on('sidebar-open', function (open) {
            vm.setCookie('sidebar-open', open);
        });
    },
}
</script>