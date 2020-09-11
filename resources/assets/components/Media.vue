<template>
    <card :title="__('voyager::generic.media')" icon="photograph">
        <div class="mt-4">
            <media-manager
                :upload-url="uploadUrl"
                :list-url="listUrl"
                :drag-text="dragText"
                :drop-text="dropText"
                :allowed-mime-types="allowedMimeTypes"
            >
            </media-manager>
        </div>
    </card>
</template>

<script>
export default {
    props: ['uploadUrl', 'listUrl', 'dragText', 'dropText', 'allowedMimeTypes'],
}
</script>