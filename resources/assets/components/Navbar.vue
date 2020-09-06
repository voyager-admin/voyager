<template>
    <nav class="flex items-center mb-3 sm:px-3 md:px-4">
        <button @click.stop="$emit('toggleSidebar')" class="button small mx-2 flex-none">
            <icon :icon="sidebarOpen ? 'dots-vertical' : 'dots-horizontal'" />
        </button>
        <search
            class="h-full flex-grow flex"
            placeholder="...."
            :mobile-placeholder="__('voyager::generic.search')"
        >
        </search>
        <user-dropdown
            photo="..."
            name="..."
            class="flex-none"
        >
        </user-dropdown>
    </nav>
</template>

<script>
export default {
    props: ['sidebarOpen'],
    emits: ['toggleSidebar'],
}
</script>