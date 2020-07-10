<template>
    <div>
        <slot v-if="show == 'query'"></slot>
        <div v-else>
            <div class="flex items-center space-x-1" v-if="options.icons">
                <div v-for="(file, i) in items.slice(0, displayItems)" :key="i" class="flex-1">
                    <div v-tooltip="file.relative_path + file.name">
                        <img :src="file.url" class="rounded-lg object-contain h-16 max-w-full" v-if="mimeMatch(file.type, 'image/*')" />
                        <icon v-else icon="document" size="16"></icon>
                    </div>
                </div>
                <span v-if="items.length > displayItems" class="italic text-sm">
                    {{ __('voyager::generic.more_results', { num: items.length - displayItems }) }}
                </span>
            </div>
            <div v-else>
                <span v-for="(file, i) in items.slice(0, displayItems)" :key="i" v-tooltip="file.relative_path + file.name">
                    {{ file.name }}<br>
                </span>
                <span v-if="items.length > displayItems" class="italic text-sm">
                    {{ __('voyager::generic.more_results', { num: items.length - displayItems }) }}
                </span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['show', 'options', 'value', 'translatable'],
    computed: {
        items: function () {
            if (this.isArray(this.value)) {
                return this.value;
            }

            return [];
        },
        displayItems: function () {
            return this.options.display || 3;
        }
    },
};
</script>