<template>
    <nav class="flex items-center mb-3 sm:px-3 md:px-4">
        <button @click.stop="$store.toggleSidebar()" class="button small mx-2 flex-none">
            <icon :icon="$store.sidebarOpen ? 'dots-vertical' : 'dots-horizontal'" />
        </button>
        <search
            class="h-full flex-grow flex"
            :placeholder="$store.searchPlaceholder"
            :mobile-placeholder="__('voyager::generic.search')"
        >
        </search>
        <div class="mt-1 text-right max-w-sm mx-auto z-30">
            <dropdown>
                <div>
                    <div class="flex items-center px-6 py-4">
                        <img class="h-10 w-10 rounded-full flex-no-shrink" :src="$store.user.avatar" alt="">
                        <div class="ltr:ml-4 rtl:mr-4">
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
                <template v-slot:opener>
                    <button class="flex items-center pl-6 py-2 font-semibold rounded-lg focus:outline-none">
                        <div class="w-48 text-right text-sm flex justify-end">
                            <img class="h-6 w-6 rounded-full flex-no-shrink" :src="$store.user.avatar" alt="">
                            <span class="hidden md:block ltr:ml-3 rtl:mr-3">
                                Hello {{ $store.user.name }}
                            </span>
                        </div>
                        <icon icon="chevron-down" :size="4" class="ltr:ml-2 rtl:mr-2" />
                    </button>
                </template>
            </dropdown>
        </div>
    </nav>
</template>

<script>
export default {
    
}
</script>