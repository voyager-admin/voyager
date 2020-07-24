<template>
    <media-manager
        :upload-url="options.upload_url || route('voyager.media.upload')"
        :list-url="options.list_url || route('voyager.media.list')"
        :drag-text="__('voyager::media.drag_files_here')"
        :drop-text="__('voyager::media.drop_files')"
        pick-files
        v-model="pickedFiles"
        :meta="options.meta || {}"
        :max="options.max || 0"
        :allowed-mime-types="options.mimes || []"
        :closed="!options.opened || false"
        @pick="pickFile($event)"
    />
</template>

<script>
export default {
    props: ['options', 'value'],
    computed: {
        pickedFiles: {
            get: function () {
                if (this.isArray(this.value)) {
                    return this.value;
                } else if (this.value == '' || this.value === null) {
                    return [];
                }

                // TODO: Consider JSON stringified values

                return [this.value];
            },
            set: function (value) {
                this.$emit('input', value);
            }
        }
    },
};
</script>