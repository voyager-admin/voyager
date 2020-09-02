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

            <label class="label mt-4">{{ __('voyager::formfields.media_picker.select_text') }}</label>
            <language-input
                class="input w-full"
                type="text" :placeholder="__('voyager::formfields.media_picker.select_text')"
                v-model="options.select_text"
            /> 
        </div>
        <div v-if="show == 'list-options'">
            <label class="label mt-4">{{ __('voyager::formfields.media_picker.show_icons') }}</label>
            <input type="checkbox" class="input" v-model.number="options.icons">

            <label class="label mt-4">{{ __('voyager::formfields.media_picker.display_items') }}</label>
            <plus-minus-input :max="50" class="input w-full" v-model.number="options.display" />

            <label class="label mt-4">{{ __('voyager::formfields.media_picker.shuffle_items') }}</label>
            <input type="checkbox" class="input" v-model.number="options.shuffle">
        </div>
        <div v-else-if="show == 'view'" class="w-full">
            <div class="flex-grow grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                <div v-for="i in 2" :key="i" class="item w-full rounded-md border cursor-pointer select-none h-auto">
                    <div class="flex p-3">
                        <div class="flex-none">
                            <div class="w-full flex justify-center">
                                <div class="w-full flex justify-center h-24">
                                    <icon icon="document" size="24"></icon>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow ml-3 overflow-hidden">
                            <div class="flex flex-col h-full">
                                <div class="flex-none">
                                    <p class="whitespace-no-wrap">{{ __('voyager::generic.file')+' '+i }}</p>
                                </div>
                                <div class="flex items-end justify-end flex-grow">
                                    <button>
                                        <icon icon="x" :size="4"></icon>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="item w-full rounded-md border cursor-pointer select-none h-auto">
                    <div class="flex p-3">
                        <div class="flex-none">
                            <div class="w-full flex justify-center">
                                <div class="w-full flex justify-center h-24">
                                    <icon icon="plus-circle" size="24"></icon>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <h5>{{ translate(options.select_text) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['options', 'column', 'show'],
};
</script>

<style lang="scss" scoped>
@import "../../../sass/mixins/bg-color";
@import "../../../sass/mixins/border-color";

.item {
    @include bg-color(media-item-bg-color-dark, 'colors.gray.800');
    @include border-color(media-item-border-color-dark, 'colors.gray.700');

    &:hover {
        @include bg-color(media-item-hover-bg-color-dark, 'colors.gray.700');
    }
}
</style>