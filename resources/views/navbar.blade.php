<nav class="h-16 flex justify-start mb-3 mx-auto sm:px-3 md:px-4">
    <div class="flex justify-between items-center w-full">
        <div class="w-full h-12 mt-4">
            <search placeholder="{{ Bread::getBreadSearchPlaceholder() }}" :mobile-placeholder="__('voyager::generic.search')" />
        </div>
        <user-dropdown photo="{{ Voyager::assetUrl('images/default-avatar.png') }}" name="{{ $authentication->name() }}" />
    </div>
</nav>