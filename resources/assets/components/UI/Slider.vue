<template>
    <div class="flex justify-center items-center">
        <div class="relative w-full">
            <div>
                <input type="range"
                    :step="step"
                    :min="min" :max="max"
                    v-model.number="lowerValue"
                    class="absolute pointer-events-none z-20 h-2 w-full cursor-pointer appearance-none opacity-0"
                >

                <input type="range" 
                    :step="step"
                    :min="min" :max="max"
                    v-model.number="upperValue"
                    class="absolute pointer-events-none z-20 h-2 w-full cursor-pointer appearance-none opacity-0"
                    v-if="range"
                >

                <div class="slider">
                    <div class="track"></div>
                    <div class="distance" :class="`bg-${color}-500`" :style="`right:${upperPos}%; left:${lowerPos}%`"></div>
                    <div class="thumb -ml-1" :class="`bg-${color}-500`" :style="`left: ${lowerPos}%`"></div>
                    <div class="thumb -mr-3" :class="`bg-${color}-500`" :style="`right: ${upperPos}%`" v-if="range"></div>
                </div>
            </div>

            <div class="flex justify-between items-center py-5" v-if="inputs">
                <input type="text" v-model.number="lowerValue" class="input small">
                <input type="text" v-model.number="upperValue" class="input small" v-if="range">
            </div>
        </div>
    </div>
</template>

<script>
export default {
    emits: ['update:lower', 'update:upper'],
    props: {
        range: {
            type: Boolean,
            default: true,
        },
        min: {
            type: Number,
            default: 0,
        },
        max: {
            type: Number,
            default: 100,
        },
        step: {
            type: Number,
            default: 1,
        },
        lower: {
            type: Number,
            default: 0,
            required: true,
        },
        upper: {
            type: Number,
            default: 100,
        },
        distance: {
            type: Number,
            default: 0,
        },
        inputs: {
            type: Boolean,
            default: true,
        },
        color: {
            type: String,
            default: 'accent',
        }
    },
    computed: {
        lowerPos() {
            return ((this.lower - this.min) / (this.max - this.min)) * 100;
        },
        upperPos() {
            return 100 - (((this.upper - this.min) / (this.max - this.min)) * 100);
        },
        lowerValue: {
            get() {
                return this.lower;
            },
            set(value) {
                if (value > (this.upper - this.distance)) {
                    value = this.upper - this.distance;
                }
                this.$emit('update:lower', value);
            }
        },
        upperValue: {
            get() {
                return this.upper;
            },
            set(value) {
                if (value < (this.lower + this.distance)) {
                    value = this.lower + this.distance;
                }
                this.$emit('update:upper', value);
            }
        },
    }
};
</script>

<style lang="scss">
@import "../../sass/mixins/bg-color";

input[type=range] {
    @apply appearance-none w-full;

    &::-webkit-slider-thumb {
        pointer-events: all;
        @apply h-6 w-6 appearance-none;
    }
}

.dark .slider {
    .track {
        @include bg-color(slider-track-color-dark, 'colors.gray.600');
    }
}

.slider {
    @apply relative z-10 h-2;

    .track {
        @apply absolute z-10 left-0 right-0 bottom-0 top-0 rounded-md;
        @include bg-color(slider-track-color, 'colors.gray.200');
    }

    .distance {
        @apply absolute z-20 top-0 bottom-0 rounded-md;
    }

    .thumb {
        @apply absolute z-30 w-6 h-6 top-0 right-0 rounded-full -mt-2;
    }
}
</style> 