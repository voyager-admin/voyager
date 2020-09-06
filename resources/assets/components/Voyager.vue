<template>
    <div was="slide-x-left-transition" class="h-screen flex overflow-hidden" id="voyager" tag="div" group>
        <div key="loader">
            <div was="fade-transition" :duration="500">
                <div class="loader" v-if="pageLoading">
                    <icon icon="helm" size="auto" class="block icon animate-spin-slow"></icon>
                </div>
            </div>
        </div>
        <sidebar :sidebarOpen="sidebarOpen" :darkmode="darkmode" @toggleDarkmode="toggleDarkMode" @toggleSidebar="toggleSidebar" @toggleDirection="toggleDirection" :menuItems="menuItems" />
        <div class="flex flex-col w-0 flex-1 overflow-hidden" key="content">
            <main class="flex-1 relative z-0 overflow-y-auto pt-2 pb-6 outline-none">
                <span id="top"></span>
                <navbar ::sidebarOpen="sidebarOpen" @toggleSidebar="toggleSidebar" />
                <div class="mx-auto sm:px-3 md:px-4" id="top">
                    {{ pageLoading }}
                </div>
            </main>
        </div>
        <notifications key="notifications"></notifications>
        <tooltips key="tooltips"></tooltips>
    </div>
</template>

<script>
import Sidebar from './Sidebar';
import Navbar from './Navbar';

export default {
    components: {
        'sidebar': Sidebar,
        'navbar': Navbar,
    },
    props: ['routes', 'localization', 'breads', 'formfields', 'debug', 'jsonOutput', 'menuItems'],
    data: function () {
        return {
            csrf_token: document.head.querySelector('meta[name="csrf-token"]').content,
            darkmode: false,
            rtl: false,
            sidebarOpen: true,
            pageLoading: true,
        };
    },
    methods: {
        toggleDirection: function () {
            this.rtl = !this.rtl;
            if (this.rtl) {
                document.querySelector('html').setAttribute('dir', 'rtl');
            } else {
                document.querySelector('html').setAttribute('dir', 'ltr');
            }
        },
        toggleDarkMode: function () {
            this.darkmode = !this.darkmode;
            if (this.darkmode) {
                document.querySelector('html').classList.add('dark');
            } else {
                document.querySelector('html').classList.remove('dark');
            }
        },
        toggleSidebar: function () {
            this.sidebarOpen = !this.sidebarOpen;
        },
        openSidebar: function () {
            this.sidebarOpen = true;
        },
        closeSidebar: function () {
            this.sidebarOpen = false;
        },
        getFormfieldByType: function (type) {
            return this.formfields.where('type', type).first();
        },
        getBreadByTable: function (table) {
            return this.breads.where('table', table).first();
        },
        handleAjaxError: function (response) {
            var notification = new this.$notification(response.data.message).color('red').timeout();
            if (response.data.hasOwnProperty('exception')) {
                notification = notification.title(response.data.exception);
            }

            return notification.show();
        },
        getCookie: function(name) {
            var name = name + '=';
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return '';
        },
        setCookie: function (name, value, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays*24*60*60*1000));
            var expires = "expires="+ d.toUTCString();
            document.cookie = name + "=" + value + ";" + expires + ";path=/";
        },
    },
    watch: {
        sidebarOpen: function (open) {
            this.setCookie('sidebar-open', (open ? 'true' : 'false'), 360);
        },
        darkmode: function (darkmode) {
            this.setCookie('dark-mode', (darkmode ? 'true' : 'false'), 360);
        },
    },
    created: function () {
        var vm = this;
        // Toggle darkmode when cookie is set or dark-mode is the prefered color scheme
        var dark_mode = vm.getCookie('dark-mode');
        if (window.matchMedia && dark_mode == '') {
            var dark = window.matchMedia('(prefers-color-scheme: dark)');
            if (dark.matches) {
                vm.toggleDarkMode(true);
            }
            // TODO: Once Safari follow the specs, this should be .dark.addEventListener('change', () => {})
            dark.addListener(function (e) {
                vm.toggleDarkMode(e.matches);
            });
        }
        if (dark_mode == 'true') {
            vm.toggleDarkMode(true);
        }

        // Toggle sidebar when cookie is set
        var sidebar_open = vm.getCookie('sidebar-open');
        if (sidebar_open == 'false') {
            vm.toggleSidebar();
        }

        // Hide loader when page is loaded
        document.addEventListener('DOMContentLoaded', function() {
            vm.pageLoading = false;
        });
    }
}
</script>