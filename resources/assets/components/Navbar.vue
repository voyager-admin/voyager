<template>
    <nav class="flex items-center mb-3 sm:px-3 md:px-4">
        <button @click.stop="$store.toggleSidebar()" class="button small mx-2 flex-none">
            <icon :icon="$store.sidebarOpen ? 'dots-vertical' : 'dots-horizontal'" />
        </button>
        <search
            class="h-full flex-grow flex"
            :placeholder="$store.searchPlaceholder"
            mobile-placeholder="{{ __('voyager::generic.search') }}"
        >
        </search>
        <user-dropdown
            :photo="$store.user.avatar"
            :name="$store.user.name"
            class="flex-none"
        >
        </user-dropdown>
    </nav>
</template>

<script>
export default {
    
}
</script>