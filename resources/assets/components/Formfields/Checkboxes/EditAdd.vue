<template>
    <div>
        <div v-for="option in options.options" :key="option.key">
            <input type="checkbox" :id="option.key" class="input" :value="option.key" v-model="selected">
            <label :for="option.key">{{ translate(option.value) }}</label>
        </div>
    </div>
</template>

<script>
export default {
    props: ['options', 'value'],
    data: function () {
        return {
            selected: [],
        };
    },
    watch: {
        selected: function (value) {
            this.$emit('input', value);
        }
    },
    mounted: function () {
        if (this.isArray(this.value)) {
            this.selected = this.value;
        }
    }
};
</script>