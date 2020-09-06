<template>
    <!-- Mobile sidebar -->
    <div v-if="sidebarOpen" class="md:hidden" :key="'mobile_sidebar'">
        <div class="fixed inset-0 z-30">
            <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
        </div>
        <div class="fixed inset-0 flex z-40" @click="$emit('toggleSidebar')">
            <div class="flex-1 flex flex-col max-w-xs w-full sidebar" @click.stop="">
                <div class="absolute top-0 right-0 p-1">
                    <button @click="$emit('toggle-sidebar')" class="flex items-center justify-center h-12 w-12 rounded-full">
                        <icon icon="x"></icon>
                    </button>
                </div>
                <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                    <div class="flex-shrink-0 flex items-center px-4">
                        <icon icon="helm" :size="10" class="icon"></icon>
                        <span class="font-black text-lg uppercase pl-2 title">
                            Voyager II
                        </span>
                    </div>
                    <nav class="mt-3 px-2">
                        <menu-wrapper
                            :items="menuItems"
                            current-url="''"
                            :parent-url="null"
                        />
                    </nav>
                </div>
                <div class="flex-shrink-0 flex border-t sidebar-border p-4">
                    <button class="button accent" @click="$emit('toggleDarkmode')">
                        <icon :icon="darkmode ? 'sun' : 'moon'"></icon>
                    </button>
                    <img src="" class="rounded-full m-4 w-8" alt="User Avatar">
                </div>
            </div>
            <div class="flex-shrink-0 w-14"></div>
        </div>
    </div>

    <!-- Desktop sidebar -->
    <div class="hidden md:flex md:flex-shrink-0 sidebar h-full" v-if="sidebarOpen" :key="'desktop_sidebar'">
        <div class="flex flex-col w-64 border-r sidebar-border">
            <div class="h-0 flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                <div class="flex items-center flex-shrink-0 px-4">
                    <icon icon="helm" :size="10" class="icon"></icon>
                    <span class="font-black text-lg uppercase ltr:pl-2 rtl:pr-2 title">
                        {{ __('voyager::generic.actions') }}
                    </span>
                </div>            
                <nav class="mt-4 flex-1 px-2">
                    <menu-wrapper
                        :items="menuItems"
                        current-url="..."
                    />
                </nav>
            </div>
            <div class="flex-shrink-0 inline-flex border-t sidebar-border p-4 h-auto overflow-x-hidden">
                <button class="button accent small" @click="$emit('toggleDarkmode')">
                    <icon :icon="darkmode ? 'sun' : 'moon'" />
                </button>
                <button class="button accent small" v-scroll-to="''">
                    <icon icon="arrow-up" />
                </button>
                <button class="button accent small" @click="$emit('toggleDirection')">
                    <icon icon="switch-horizontal" />
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import MenuWrapper from './Layout/MenuWrapper.vue';

export default {
    props: ['sidebarOpen', 'darkmode', 'menuItems'],
    emits: ['toggleDarkmode', 'toggleSidebar', 'toggleDirection'],
    components: {
        'menu-wrapper': MenuWrapper,
    },
}
</script>