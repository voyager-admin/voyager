<template>
    <select
        class="input w-full"
        :multiple="options.multiple || false"
        v-model="selected">
        <option v-for="option in options.options" :value="parsedKey(option.key)" :key="option.key">
            {{ translate(option.value, true) }}
        </option>
    </select>
</template>

<script>
export default {
    props: ['options', 'modelValue', 'show'],
    emits: ['update:modelValue'],
    data: function () {
        return {
            selected: (this.options.multiple || false ? [] : ''),
        };
    },
    created: function () {
        this.$watch(
            () => this.modelValue,
            function () {
                this.parseInput();
            },
            { immediate: true }
        );
        this.$watch(
            () => this.selected,
            function (value) {
                this.$emit('update:modelValue', this.parsedKey(value));
            }
        );
    },
    methods: {
        parseInput: function () {
            if ((this.options.multiple || false) && this.isString(this.modelValue)) {
                try {
                    this.selected = JSON.parse(this.modelValue);
                } catch (e) {
                    this.selected = [];
                }
            } else {
                this.selected = this.modelValue;
            }
        },
        parsedKey: function (key) {
            if (key == 'true') {
                return true;
            } else if (key == 'false') {
                return false;
            }

            return key;
        }
    },
};
</script>