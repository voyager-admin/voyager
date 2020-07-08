<template>
    <div>
        <div v-if="show == 'view-options'">
            <label class="label mt-4">{{ __('voyager::generic.max') }}</label>
            <input type="number" min="0" max="100000" class="input w-full" v-model.number="options.max">

            <key-value-form
                class="mt-2"
                v-model="options.meta"
                title-text="voyager::formfields.media_picker.meta_fields"
                key-text="voyager::generic.property"
                value-text="voyager::generic.title"
            />
        </div>
        <div v-if="show == 'list-options'">
            
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