<template>
    <div class="input flex items-center space-x-1">
        <input type="number" class="bg-transparent border-0 focus:outline-none w-full" v-model="reactiveValue" :min="min" :max="max" :step="step">
        <icon icon="plus-circle" :size="6" class="cursor-pointer" @click.prevent.stop="plus" />
        <icon icon="minus-circle" :size="6" class="cursor-pointer" @click.prevent.stop="minus" />
    </div>
</template>

<script>
export default {
    props: {
        value: {
            type: Number,
            required: true,
        },
        min: {
            type: Number,
        },
        max: {
            type: Number,
        },
        step: {
            type: Number,
            default: 1,
        }
    },
    data: function () {
        return {
            reactiveValue: this.value
        };
    },
    methods: {
        plus: function () {
            if (this.max !== undefined && (this.reactiveValue + this.step) > this.max) {
                return;
            }

            this.reactiveValue += this.step;
        },
        minus: function () {
            if (this.min !== undefined && (this.reactiveValue - this.step) < this.min) {
                return;
            }

            this.reactiveValue -= this.step;
        }
    },
    watch: {
        reactiveValue: function (value) {
            this.$emit('input', value);
        },
        value: function (value) {
            this.reactiveValue = value;
        }
    }
}
</script>