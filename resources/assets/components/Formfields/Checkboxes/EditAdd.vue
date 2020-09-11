<template>
    <div v-for="option in options.options" :key="option.key">
        <input type="checkbox" :id="option.key" class="input" :value="option.key" v-model="selected">
        <label :for="option.key">{{ translate(option.value) }}</label>
    </div>
</template>

<script>
export default {
    props: ['options', 'modelValue', 'show'],
    emits: ['update:modelValue'],
    data: function () {
        return {
            selected: [],
        };
    },
    created: function () {
        this.$watch(
            () => this.selected,
            function (value) {
                this.$emit('update:modelValue', value);
            }
        );
        this.$watch(
            () => this.modelValue,
            function (value) {
                if (this.isArray(value)) {
                    this.selected = value;
                }
            },
            { immediate: true }
        );
    },
};
</script>