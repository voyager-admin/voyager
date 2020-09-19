<template>
    <div>
        <div v-if="palette == 'tailwind-colors'" class="w-full inline-flex justify-center">
            <tooltip v-for="(color, key) in colors" :key="'color-'+key" :value="__('voyager::generic.color_names.'+color)">
                <button
                    @click="$emit('update:modelValue', color); current = color"
                    class="button mb-2" :class="[color]"
                >
                    <input type="radio" class="input opacity-75" :checked="current == color">
                </button>
            </tooltip>
        </div>
    </div>
</template>
<script>
export default {
    emits: ['update:modelValue'],
    props: {
        palette: {
            type: String,
            default: 'tailwind-colors',
            validator: function (value) {
                return ['tailwind-colors'].indexOf(value) !== -1;
            }
        },
        modelValue: {
            type: String,
            default: 'blue',
        }
    },
    data: function () {
        return {
            current: this.modelValue
        };
    }
};
</script>