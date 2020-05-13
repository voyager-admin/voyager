<template>
    <div>
        <div v-if="palette == 'tailwind-colors'" class="w-full text-center">
            <button
                v-for="(color, key) in $store.ui.colors" :key="'color-'+key"
                @click="$emit('input', color); current = color"
                class="button mb-2 icon-only" :class="[color]">
                <icon :icon="current == color ? 'check-circle' : 'circle'"></icon>
            </button>
        </div>
        <div v-if="palette == 'tailwind-shades'" class="w-full text-center">
            <div class="grid grid-rows-9 grid-flow-col gap-1">
                <div v-for="(shade, i) in [100, 200, 300, 400, 500, 600, 700, 800, 900]" :key="'shade-'+i">
                    <button
                        v-for="(color, key) in $store.ui.colors" :key="'color-'+key"
                        @click="$emit('input', color+'-'+shade); current = color+'-'+shade"
                        class="button mb-2 icon-only" :class="`bg-${color}-${shade}`">
                        <icon :icon="current == color+'-'+shade ? 'check-circle' : 'circle'"></icon>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        palette: {
            type: String,
            default: 'tailwind-colors',
            validator: function (value) {
                return ['tailwind-colors', 'tailwind-shades'].indexOf(value) !== -1;
            }
        },
        value: {
            type: String,
            default: 'blue',
        }
    },
    data: function () {
        return {
            current: this.value
        };
    }
};
</script>

<style scoped>

</style>