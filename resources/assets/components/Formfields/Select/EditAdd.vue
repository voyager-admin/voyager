<template>
    <div>
        <select
            class="input w-full"
            :multiple="options.multiple || false"
            v-model="selected">
            <option v-for="option in options.options" :value="parsedKey(option.key)" :key="option.key">
                {{ translate(option.value, true) }}
            </option>
        </select>
    </div>
</template>

<script>
export default {
    props: ['options', 'modelValue'],
    emits: ['update:modelValue'],
    data: function () {
        return {
            selected: (this.options.multiple || false ? [] : ''),
        };
    },
    watch: {
        modelValue: {
            immediate: true,
            handler: function (value) {
                this.parseInput();
            }
        },
        selected: function (value) {
            this.$emit('update:modelValue', this.parsedKey(value));
        }
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