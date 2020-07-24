<template>
    <div>
        <div v-if="show == 'view-options'">
            <label class="label mt-4">{{ __('voyager::generic.max') }}</label>
            <plus-minus-input class="input w-full" v-model.number="options.max" />

            <key-value-form
                class="mt-2"
                v-model="options.meta"
                title-text="voyager::formfields.media_picker.meta_fields"
                key-text="voyager::generic.property"
                value-text="voyager::generic.title"
            />

            <array-form
                class="mt-2"
                v-model="options.mimes"
                title-text="voyager::formfields.media_picker.mime_types"
                item-text="voyager::generic.type"
            />

            <label>{{ __('voyager::formfields.media_picker.open_by_default') }}</label>
            <input type="checkbox" class="input" v-model="options.opened" />
        </div>
        <div v-if="show == 'list-options'">
            <label class="label mt-4">{{ __('voyager::formfields.media_picker.show_icons') }}</label>
            <input type="checkbox" class="input" v-model.number="options.icons">

            <label class="label mt-4">{{ __('voyager::formfields.media_picker.display_items') }}</label>
            <plus-minus-input :max="50" class="input w-full" v-model.number="options.display" />

            <label class="label mt-4">{{ __('voyager::formfields.media_picker.shuffle_items') }}</label>
            <input type="checkbox" class="input" v-model.number="options.shuffle">
        </div>
        <div v-else-if="show == 'view'">
            <media-manager
                :upload-url="options.upload_url || route('voyager.media.upload')"
                :list-url="options.list_url || route('voyager.media.list')"
                :drag-text="__('voyager::media.drag_files_here')"
                :drop-text="__('voyager::media.drop_files')"
                ref="media">
            </media-manager>
        </div>
    </div>
</template>

<script>
export default {
    props: ['options', 'column', 'show'],
};
</script>