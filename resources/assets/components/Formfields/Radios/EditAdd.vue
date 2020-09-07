<template>
    <div v-for="option in options.options" :key="option.key">
        <input type="radio" :id="option.key" class="input" :value="option.key" v-model="selected">
        <label :for="option.key">{{ translate(option.value) }}</label>
    </div>
</template>

<script>
export default {
    emits: ['update:modelValue'],
    props: ['options', 'modelValue'],
    data: function () {
        return {
            selected: this.modelValue,
        };
    },
    watch: {
        selected: function (value) {
            this.$emit('update:modelValue', value);
        }
    },
};
</script>