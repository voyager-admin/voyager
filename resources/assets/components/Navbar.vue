<template>
    <nav class="flex items-center mb-3 sm:px-3 md:px-4">
        <button @click.stop="toggleSidebar()" class="button small mx-2 flex-none" aria-label="Toggle navbar">
            <icon :icon="$store.sidebarOpen ? 'dots-vertical' : 'dots-horizontal'" />
        </button>
        <Search
            class="h-full flex-grow flex"
            :placeholder="shared.search_placeholder"
            :mobile-placeholder="__('voyager::generic.search')"
        />
        <div class="mt-1 text-right max-w-sm mx-auto z-30">
            <dropdown placement="bottom-end">
                <div>
                    <div class="flex items-center px-6 py-4">
                        <img class="h-10 w-10 rounded-full flex-no-shrink" :src="shared.user.avatar" alt="">
                        <div class="ml-4">
                        <p class="font-semibold leading-none">{{ shared.user.name }}</p>
                        <p>
                            <a href="#" class="text-sm leading-none hover:underline">
                                {{ __('voyager::generic.view_profile') }}
                            </a>
                        </p>
                        </div>
                    </div>
                    <a :href="route('voyager.dashboard')" class="link">
                        {{ __('voyager::generic.dashboard') }}
                    </a>
                    <a :href="route('voyager.settings.index')" class="link">
                        {{ __('voyager::generic.settings') }}
                    </a>
                    <a :href="route('voyager.logout')" class="link">
                        {{ __('voyager::auth.logout') }}
                    </a>
                </div>
                <template #opener>
                    <button class="flex items-center pl-6 py-2 font-semibold rounded-lg focus:outline-none">
                        <div class="w-48 text-right text-sm flex justify-end">
                            <img class="h-6 w-6 rounded-full flex-no-shrink" :src="shared.user.avatar" alt="">
                            <span class="hidden md:block ml-3">
                                {{ __('voyager::generic.hello_user', { user: shared.user.name }) }}
                            </span>
                        </div>
                        <icon icon="chevron-down" :size="4" class="ml-2" />
                    </button>
                </template>
            </dropdown>
        </div>
    </nav>
</template>

<script>
import { usePage } from '@inertiajs/inertia-vue3';

import Search from './Layout/Search';

export default {
    components: {
        Search
    },
    computed: {
        shared() {
            return usePage().props.value;
        }
    }
}
</script>