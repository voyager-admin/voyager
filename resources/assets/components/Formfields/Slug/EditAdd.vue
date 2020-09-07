<template>
    <input
        type="text"
        class="input w-full"
        v-bind:value="value || translate(options.default_value, true)"
        @input="value = slugifyValue($event.target.value)"
        :placeholder="translate(options.placeholder, true)">
</template>

<script>
export default {
    emits: ['update:modelValue'],
    props: ['options', 'modelValue'],
    data: function () {
        return {
            value: this.slugifyValue(this.modelValue)
        };
    },
    methods: {
        slugifyValue: function (value) {
            return this.slugify(value, {
                replacement: this.options.replacement || '-',
                lower: this.options.lower || false,
                strict: this.options.strict || false,
            });
        }
    },
    mounted: function () {
        var vm = this;

        // TODO
        /*EventBus.$on('input', function (parameters) {
            if (parameters.column.type == 'column' && parameters.column.column == vm.options.field) {
                vm.value = vm.slugifyValue(parameters.value);
            }
        });*/
    },
    watch: {
        modelValue: function (value) {
            this.reactiveValue = value;
        },
        value: function (value) {
            this.$emit('update:modelValue', this.slugifyValue(value));
        }
    }
};
</script>