<template>
    <div class="w-full">
        <div class="flex-grow grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
            <div v-for="(file, i) in data" :key="i" class="item w-full rounded-md border cursor-pointer select-none h-auto">
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
                                <div class="space-y-1">
                                    <div v-for="(field, m) in options.meta" :key="m">
                                        {{ translate(field.value) }}: {{ translate(file.meta[field.key]) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['options', 'data'],
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