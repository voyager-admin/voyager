<template>
    <slot v-if="action == 'query'"></slot>
    <div v-else-if="action == 'browse'">
        <div class="flex items-center space-x-1" v-if="options.icons === true">
            <div v-for="(file, i) in slicedItems" :key="i">
                <div v-tooltip="file.relative_path + file.name">
                    <img :src="file.url" class="rounded-lg object-contain h-16 max-w-full" v-if="matchMime(file.type, 'image/*')" />
                    <icon v-else icon="document" size="16" />
                </div>
            </div>
            <span v-if="modelValue.length > options.display" class="italic text-sm">
                {{ __('voyager::generic.more_results', { num: modelValue.length - options.display }) }}
            </span>
        </div>
        <div v-else>
            <span v-for="(file, i) in slicedItems" :key="i">
                {{ file.name }}b<br>
            </span>
            <span v-if="modelValue.length > options.display" class="italic text-sm">
                {{ __('voyager::generic.more_results', { num: modelValue.length - options.display }) }}
            </span>
        </div>
    </div>
    <div v-else-if="action == 'edit' || action == 'add'" class="w-full">
        <div class="flex-grow grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
            <div v-for="(file, i) in modelValue" :key="i" class="item w-full rounded-md border cursor-pointer select-none h-auto">
                <div class="flex p-3">
                    <div class="flex-none">
                        <div class="w-full flex justify-center">
                            <img :src="file.url" class="rounded object-contain h-24 max-w-full" v-if="matchMime(file.type, 'image/*')" />
                            <div v-else class="w-full flex justify-center h-24">
                                <icon icon="document" size="24" />
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow ml-3 overflow-hidden">
                        <div class="flex flex-col h-full">
                            <div class="flex-none">
                                <div class="w-full flex">
                                    <div v-tooltip="file.name" class="flex-grow">
                                        <p class="whitespace-nowrap">{{ file.name }}</p>
                                    </div>
                                    <div class="flex-grow-0">
                                        <button @click.stop="removeFile(file)">
                                            <icon icon="x" :size="4" />
                                        </button>
                                    </div>
                                </div>
                                <div v-for="(field, m) in options.meta" :key="m" class="mt-1">
                                    <language-input
                                        class="input w-full small"
                                        type="text" :placeholder="translate(field.value, true)"
                                        v-model="file.meta[field.key]"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <modal :title="translate(options.select_text || __('voyager::media.select_upload_files'), true)" size="full">
                <media-manager
                    v-model="value"
                    :uploadUrl="route('voyager.media.upload')"
                    :listUrl="route('voyager.media.list')"
                    :allowedMimeTypes="[]"
                />
                <template #opener>
                    <div class="item w-full rounded-md border cursor-pointer select-none h-auto">
                        <div class="flex p-3">
                            <div class="flex-none">
                                <div class="w-full flex justify-center">
                                    <div class="w-full flex justify-center h-24">
                                        <icon icon="plus-circle" size="24" />
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <h6>{{ translate(options.select_text || __('voyager::media.select_upload_files'), true) }}</h6>
                            </div>
                        </div>
                    </div>
                </template>
            </modal>
        </div>
    </div>
    <div v-else>
        <div class="flex-grow grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
            <div v-for="(file, i) in modelValue" :key="i" class="item w-full rounded-md border cursor-pointer select-none h-auto">
                <div class="flex p-3">
                    <div class="flex-none">
                        <div class="w-full flex justify-center">
                            <img :src="file.url" class="rounded object-contain h-24 max-w-full" v-if="matchMime(file.type, 'image/*')" />
                            <div v-else class="w-full flex justify-center h-24">
                                <icon icon="document" size="24" />
                            </div>
                        </div>
                    </div>
                    <div class="ml-3 overflow-hidden" v-tooltip="file.name">
                        <p class="whitespace-nowrap">{{ file.name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import formfield from '../../../js/mixins/formfield';
import matchMime from '../../../js/helper/match-mime';

export default {
    mixins: [formfield],
    computed: {
        value: {
            get() {
                if (this.isArray(this.modelValue)) {
                    return this.modelValue;
                }

                return [];
            },
            set(value) {
                if (this.options.max > 0 && this.value.length >= this.options.max) {
                    if (this.options.max === 1) {
                        // Replace the file instead of failing with an error message
                        var file = value.filter((f) => {
                            return !this.modelValue.includes(f);
                        }).first();
                        value = [file];
                    } else {
                        new this.$notification(this.trans_choice('voyager::formfields.media_picker.max_warning', this.options.max)).color('yellow').timeout().show();
                        return;
                    }
                }
                if (this.isArray(this.options.meta)) {
                    var meta = this.options.meta;

                    value.map((val) => {
                        val.meta = {};

                        meta.forEach((m) => {
                            val.meta[m.key] = '';
                        });
                    });
                }

                this.$emit('update:modelValue', value);
            }
        },
        slicedItems() {
            var items = (this.modelValue || []).slice(0, (this.options.items || 3));
            if (this.options.shuffle === true) {
                return items.shuffle();
            }

            return items;
        },
    },
    methods: {
        matchMime: matchMime,
        removeFile(file) {
            if (this.isArray(this.modelValue)) {
                this.modelValue.forEach((f, i) => {
                    if (f.disk == file.disk && file.relative_path == f.relative_path && f.name == file.name) {
                        this.$emit('update:modelValue', this.modelValue.removeAtIndex(i));
                    }
                });
            }
        }
    },
}
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