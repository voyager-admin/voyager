<template>
    <div class="input flex items-center space-x-1">
        <input
            type="number"
            class="bg-transparent border-0 focus:outline-none w-full"
            :min="min"
            :max="max"
            :step="step"
            :placeholder="placeholder"
            v-model.number="reactiveValue"
        >
        <icon icon="plus-circle" :size="6" class="cursor-pointer" @click.prevent.stop="plus" />
        <icon icon="minus-circle" :size="6" class="cursor-pointer" @click.prevent.stop="minus" />
    </div>
</template>

<script>
export default {
    props: {
        value: {
            required: true,
        },
        min: {
            default: 0,
        },
        max: {
            type: Number,
        },
        step: {
            default: 1,
        },
        placeholder: {
            type: String,
            default: '',
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

            if (this.reactiveValue < this.min) {
                this.reactiveValue = this.min;
            }
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