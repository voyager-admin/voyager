<template>
    <div>
        <slot v-if="show == 'query'"></slot>
        <div v-else>
            <div class="inline-flex items-center space-x-1 space-y-1" v-if="options.icons">
                <div v-for="(file, i) in items.slice(0, 3)" :key="i">
                    <div v-tooltip="file.relative_path + file.name">
                        <img :src="file.url" class="rounded object-contain h-16 max-w-full" v-if="mimeMatch(file.type, 'image/*')" />
                        <div v-else class="h-16">
                            <icon icon="document" size="16"></icon>
                        </div>
                    </div>
                </div>
                <span v-if="items.length > 3" class="italic text-sm">
                    {{ __('voyager::generic.more_results', { num: items.length - 3 }) }}
                </span>
            </div>
            <div v-else>
                <span v-for="(file, i) in items.slice(0, 3)" :key="i" v-tooltip="file.relative_path + file.name">
                    {{ file.name }}<br>
                </span>
                <span v-if="items.length > 3" class="italic text-sm">
                    {{ __('voyager::generic.more_results', { num: items.length - 3 }) }}
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
        }
    },
};
</script>