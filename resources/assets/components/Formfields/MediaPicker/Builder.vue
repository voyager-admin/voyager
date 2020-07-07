<template>
    <div>
        <div v-if="show == 'view-options'">
            
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