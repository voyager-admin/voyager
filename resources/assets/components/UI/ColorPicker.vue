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
    </div>
</template>
<script>
export default {
    props: {
        palette: {
            type: String,
            default: 'tailwind-colors',
            validator: function (value) {
                return ['tailwind-colors'].indexOf(value) !== -1;
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