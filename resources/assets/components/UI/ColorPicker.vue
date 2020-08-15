<template>
    <div>
        <div v-if="palette == 'tailwind-colors'" class="w-full text-center">
            <button
                v-for="(color, key) in $store.ui.colors" :key="'color-'+key"
                v-tooltip="__('voyager::generic.color_names.'+color)"
                @click="$emit('input', color); current = color"
                class="button mb-2" :class="[color]"
            >
                <input type="radio" class="input opacity-75" :checked="current == color">
            </button>
        </div>
        <div v-if="palette == 'tailwind-shades'" class="w-full text-center">
            <div class="grid grid-rows-9 gap-1">
                <div v-for="(shade, i) in [100, 200, 300, 400, 500, 600, 700, 800, 900]" :key="'shade-'+i">
                    <button
                        v-for="(color, key) in $store.ui.colors.whereNot('accent')" :key="'color-'+key"
                        v-tooltip="__('voyager::generic.color_names.'+color) + ' ' + shade"
                        @click="$emit('input', color+'-'+shade); current = color+'-'+shade"
                        class="inline-flex items-center mx-1 px-3 py-1.5 rounded-md focus:outline-none" :class="`bg-${color}-${shade}`"
                    >
                        <input type="radio" class="input opacity-75" :checked="current == color+'-'+shade">
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