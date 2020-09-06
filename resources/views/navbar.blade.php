<nav class="flex items-center mb-3 sm:px-3 md:px-4">
    <button @click.stop="$store.toggleSidebar()" class="button small mx-2 flex-none">
        <icon :icon="$store.sidebarOpen ? 'dots-vertical' : 'dots-horizontal'" />
    </button>
    <search
        class="h-full flex-grow flex"
        placeholder="{{ resolve(\Voyager\Admin\Manager\Breads::class)->getBreadSearchPlaceholder() }}"
        mobile-placeholder="{{ __('voyager::generic.search') }}"
    >
    </search>
    <user-dropdown
        photo="{{ Voyager::assetUrl('images/default-avatar.png') }}"
        name="{{ Voyager::auth()->name() }}"
        class="flex-none"
    >
    </user-dropdown>
</nav>