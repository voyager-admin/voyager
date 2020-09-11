<template>
    <div class="input flex items-center space-x-1">
        <input
            type="number"
            class="bg-transparent border-0 focus:outline-none w-full"
            :min="min"
            :max="max"
            :step="step"
            :placeholder="placeholder"
            :inputmode="inputmode"
            v-model.number="value"
        >
        <icon icon="plus-circle" :size="6" class="cursor-pointer" @click.prevent.stop="plus" />
        <icon icon="minus-circle" :size="6" class="cursor-pointer" @click.prevent.stop="minus" />
    </div>
</template>

<script>
export default {
    emits: ['update:modelValue'],
    props: {
        modelValue: {
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
            value: this.modelValue
        };
    },
    computed: {
        inputmode: function () {
            if (this.step % 1 === 0) {
                return 'numeric';
            }

            return 'decimal';
        }
    },
    methods: {
        plus: function () {
            if (this.max !== undefined && (this.value + this.step) > this.max) {
                return;
            }

            this.value += this.step;

            if (this.value < this.min) {
                this.value = this.min;
            }
        },
        minus: function () {
            if (this.min !== undefined && (this.value - this.step) < this.min) {
                return;
            }

            this.value -= this.step;
        }
    },
    created: function () {
        this.$watch(
            () => this.value,
            function (value) {
                this.$emit('update:modelValue', value);
            }
        );
        this.$watch(
            () => this.modelValue,
            function (value) {
                this.value = value;
            }
        );
    },
}
</script>