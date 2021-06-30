<template>
    <div>
        <div v-if="palette == 'tailwind-colors'" class="w-full flex flex-wrap space-x-1 justify-center">
            <div
                v-for="(color, key) in allColors" :key="'color-'+key"
                @click="$emit('update:modelValue', color)"
                v-tooltip="color ? __('voyager::generic.color_names.'+color) : __('voyager::generic.none')"
                class="rounded-full flex items-center justify-center border-2 cursor-pointer p-0.5 border-gray-500 transition"
                :class="[modelValue == color ? 'border-opacity-100' : 'border-opacity-25', `h-${sizes[size-1]} w-${sizes[size-1]}`]"
                uses="border-opacity-100 border-opacity-25"
            >
                <div class="rounded-full flex items-center justify-center w-full h-full" :class="`bg-${color}-500`" v-if="color"></div>
                <div class="rounded-full flex items-center justify-center w-full h-full text-red-500" v-else>
                    <icon icon="x-circle" :size="128" />
                </div>
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
        addNone: {
            type: Boolean,
            default: false
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
    computed: {
        allColors() {
            if (this.addNone) {
                return [null, ...this.colors]
            }

            return this.colors;
        }
    },
    data() {
        return {
            sizes: [6, 8, 10, 12, 14, 16, 20, 24, 28, 32],
        };
    }
};
</script>