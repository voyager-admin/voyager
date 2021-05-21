<template>
    <nav class="flex items-center mb-3 sm:px-3 md:px-4">
        <button @click.stop="toggleSidebar()" class="button small mx-2 flex-none" aria-label="Toggle navbar">
            <icon :icon="$store.sidebarOpen ? 'dots-vertical' : 'dots-horizontal'" />
        </button>
        <Search
            class="h-full flex-grow flex"
            :placeholder="$store.searchPlaceholder"
            :mobile-placeholder="__('voyager::generic.search')"
        />
        <div class="mt-1 text-right z-30">
            <dropdown placement="bottom-end">
                <div>
                    <div class="flex space-x-4 items-center px-6 py-4">
                        <img class="h-10 w-10 rounded-full flex-no-shrink" :src="$store.user.avatar" alt="">
                        <div>
                            <p class="font-semibold leading-none">{{ $store.user.name }}</p>
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
                    <button class="inline-flex justify-end w-48 max-w-sm space-x-2 items-center font-semibold focus:outline-none">
                        <img class="h-6 w-6 rounded-full flex-no-shrink" :src="$store.user.avatar" alt="">
                        <span class="hidden md:block whitespace-no-wrap">
                            {{ __('voyager::generic.hello_user', { user: $store.user.name }) }}
                        </span>
                        <icon icon="chevron-down" :size="4" />
                    </button>
                </template>
            </dropdown>
        </div>
    </nav>
</template>

<script>
import Search from './Layout/Search';

export default {
    components: {
        Search
    },
}
</script>