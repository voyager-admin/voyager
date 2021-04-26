<template>
    <div :class="!isLogin ? 'h-screen flex overflow-hidden' : null ">
        <div>
            <fade-transition tag="div" :duration="250">
                <div class="absolute w-full h-1.5 overflow-hidden" v-if="$store.pageLoading">
                    <div class="indeterminate">
                        <div class="before rounded" :class="`bg-blue-500`"></div>
                        <div class="after rounded" :class="`bg-blue-500`"></div>
                    </div>
                </div>
            </fade-transition>
        </div>
        <template v-if="!isLogin">
            <sidebar />
            <div class="flex flex-col w-0 flex-1 overflow-hidden">
                <main class="flex-1 relative z-0 overflow-y-auto pt-2 pb-6 outline-none" id="content">
                    <navbar></navbar>
                    <div class="mx-auto sm:px-3 md:px-4" id="top">
                        <!-- Content -->
                        <slot />
                        <!-- End content -->
                    </div>
                </main>
            </div>
        </template>
        <template v-else>
            <slot />
        </template>
        <notifications :position="tooltipPosition" />
    </div>
</template>

<script>
import Sidebar from './Sidebar';
import Navbar from './Navbar';
import Notifications from './UI/Notifications';
import $store from '../js/store';
import { watch } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';

export default {
    components: {
        'sidebar': Sidebar,
        'navbar': Navbar,
        'notifications': Notifications
    },
    created() {
        watch(() => usePage().props.value.title, (title) => {
            document.title = title + ' - ' + usePage().props.value.admin_title;
        }, { immediate: true });

        $eventbus.on('setting-updated', (s) => {
            if (s.group == 'admin' && s.key == 'title') {
                usePage().props.value.admin_title = this.translate(s.value);
                usePage().props.value.title = usePage().props.value.title;
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey) {
                if (e.key === 's') {
                    if ($eventbus.hasListener('ctrl-s-combo')) {
                        $eventbus.emit('ctrl-s-combo', e);
                        e.preventDefault();
                    }
                } else if (e.code == 'ArrowRight' || e.code == 'ArrowUp') {
                    this.nextLocale();
                } else if (e.code == 'ArrowLeft' || e.code == 'ArrowDown') {
                    this.previousLocale();
                }
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            this.$store.pageLoading = false;
        });

        Inertia.on('start', () => {
            this.$store.pageLoading = true;
        });

        Inertia.on('finish', () => {
            this.$store.pageLoading = false;
        });

        // Toggle sidebar when cookie is set
        var sidebar_open = this.getCookie('sidebar-open');
        if (sidebar_open == 'false') {
            this.closeSidebar();
        }
        $eventbus.on('sidebar-open', (open) => {
            this.setCookie('sidebar-open', open);
        });

        $store.formfields = usePage().props.value.formfields;
        $store.breads = usePage().props.value.breads;
    },
    computed: {
        tooltipPosition() {
            return usePage().props.value.tooltip_position || '';
        },
        isLogin() {
            return usePage().component.value == 'Login';
        }
    },
}
</script>