<template>
    <div class="w-full">
        <div class="flex-grow grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
            <div v-for="(file, i) in value" :key="i" class="item w-full rounded-md border cursor-pointer select-none h-auto">
                <div class="flex p-3">
                    <div class="flex-none">
                        <div class="w-full flex justify-center">
                            <img :src="file.url" class="rounded object-contain h-24 max-w-full" v-if="mimeMatch(file.type, 'image/*')" />
                            <div v-else class="w-full flex justify-center h-24">
                                <icon icon="document" size="24"></icon>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow ml-3 overflow-hidden">
                        <div class="flex flex-col h-full">
                            <div class="flex-none">
                                <p class="whitespace-no-wrap" v-tooltip="file.name">{{ file.name }}</p>
                                <div v-for="(field, m) in options.meta" :key="m" class="mt-1">
                                    <language-input
                                        class="input w-full small"
                                        type="text" :placeholder="translate(field.value, true)"
                                        v-bind:value="file.meta[field.key]"
                                        v-on:input="file.meta[field.key] = $event"
                                    />
                                </div>
                            </div>
                            <div class="flex items-end justify-end flex-grow">
                                <button @click.stop="removeFile(file)">
                                    <icon icon="x" :size="4"></icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="item w-full rounded-md border cursor-pointer select-none h-auto" @click="$refs.mediamodal.open()">
                <div class="flex p-3">
                    <div class="flex-none">
                        <div class="w-full flex justify-center">
                            <div class="w-full flex justify-center h-24">
                                <icon icon="plus-circle" size="24"></icon>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <h6>{{ translate(options.select_text, true) }}</h6>
                    </div>
                </div>
            </div>
        </div>
        <modal ref="mediamodal" size="full" :title="translate(options.select_text, true)" icon="plus-circle">
            <media-manager
                :upload-url="options.upload_url || route('voyager.media.upload')"
                :list-url="options.list_url || route('voyager.media.list')"
                :drag-text="__('voyager::media.drag_files_here')"
                :drop-text="__('voyager::media.drop_files')"
                :allowed-mime-types="options.mimes || []"
                @select="selectFile"
                :pickedFiles="value"
            />
        </modal>
    </div>
</template>

<script>
export default {
    props: ['options', 'value'],
    methods: {
        selectFile: function (file) {
            var obj = this.getFileObject(file);
            if (this.options.max === 1) {
                this.$emit('input', [obj]);
            } else {
                if (this.options.max > 1 && this.value.length >= this.options.max) {
                    new this.$notification(this.trans_choice('voyager::formfields.media_picker.max_warning', this.options.max))
                    .color('orange').timeout().show();
                    return;
                } else {
                    if (this.value.where('disk', file.disk).where('path', file.relative_path).where('name', file.name).where('type', file.type).first() !== undefined) {
                        this.removeFile(file);
                    } else {
                        this.$emit('input', [...this.value, obj]);
                    }
                }
            }
        },
        removeFile: function (file) {
            this.$emit('input', this.value.filter(function (f) {
                return !(f.disk == file.disk && (f.path == file.relative_path || f.path == file.path) && f.name == file.name && f.type == file.type); 
            }));
        },
        getFileObject: function (file) {
            var meta = {};

            this.options.meta.forEach(function (m) {
                Vue.set(meta, m.key, '');
            });

            return {
                disk: file.disk,
                path: file.relative_path,
                name: file.name,
                type: file.type,
                url: file.url,
                thumbnails: this.getThumbnails(file),
                meta: meta
            };
        },
        getThumbnails: function (file) {
            var thumbnails = [];
            file.thumbnails.forEach(function (thumb) {
                thumbnails.push({
                    disk: thumb.file.disk,
                    path: thumb.file.relative_path,
                    name: thumb.file.name,
                    type: thumb.file.type,
                    url: thumb.file.url,
                    thumbnail: thumb.thumbnail,
                });
            });

            return thumbnails;
        },
    },
};
</script>

<style lang="scss" scoped>
@import "../../../sass/mixins/bg-color";
@import "../../../sass/mixins/border-color";
@import "../../../sass/mixins/text-color";

.dark .item {
    @include bg-color(media-item-bg-color-dark, 'colors.gray.800');
    @include border-color(media-item-border-color-dark, 'colors.gray.700');

    &:hover {
        @include bg-color(media-item-hover-bg-color-dark, 'colors.gray.700');
    }

    &.selected {
        @include bg-color(media-item-selected-bg-color-dark, 'colors.gray.750');
        @include border-color(media-item-selected-border-color-dark, 'colors.blue.700');
    }

    &.picked {
        @include bg-color(media-item-picked-bg-color-dark, 'colors.gray.750');
        @include border-color(media-item-picked-border-color-dark, 'colors.green.700');
    }
}

.item {
    @include bg-color(media-item-bg-color, 'colors.gray.100');
    @include border-color(media-item-border-color, 'colors.gray.300');

    &:hover {
        @include bg-color(media-item-hover-bg-color, 'colors.gray.200');
    }

    &.selected {
        @include bg-color(media-item-selected-bg-color, 'colors.gray.250');
        @include border-color(media-item-selected-border-color, 'colors.blue.300');
    }

    &.picked {
        @include bg-color(media-item-picked-bg-color, 'colors.gray.250');
        @include border-color(media-item-picked-border-color, 'colors.green.300');
    }
}
</style>