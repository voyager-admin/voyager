<template>
    <div>
        <div v-if="palette == 'tailwind-colors'" class="w-full flex flex-wrap justify-center">
            <div
                v-for="(color, key) in colors" :key="'color-'+key"
                @click="$emit('update:modelValue', color); current = color"
                v-tooltip="__('voyager::generic.color_names.'+color)"
                class="rounded-full flex items-center justify-center border-2 cursor-pointer p-0.5"
                :class="[current == color ? 'border-gray-500' : 'border-transparent', `h-${sizes[size-1]} w-${sizes[size-1]}`]"
            >
                <div class="rounded-full flex items-center justify-center w-full h-full" :class="`bg-${color}-500`"></div>
            </div>
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
            validator: (value) => {
                return ['tailwind-colors'].indexOf(value) !== -1;
            }
        },
        modelValue: {
            type: String,
            default: 'blue',
        },
        size: {
            type: Number,
            default: 4,
            validator: (value) => {
                return value >= 1 && value <= 10;
            }
        }
    },
    data() {
        return {
            current: this.modelValue,
            sizes: [6, 8, 10, 12, 14, 16, 20, 24, 28, 32],
        };
    }
};
</script>