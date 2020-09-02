<template>
    <div>
        <input
            type="text"
            class="input w-full"
            v-bind:value="reactiveValue || translate(options.default_value, true)"
            @input="reactiveValue = slugifyValue($event.target.value)"
            :placeholder="translate(options.placeholder, true)">
    </div>
</template>

<script>
import EventBus from '../../../js/eventbus';

export default {
    props: ['options', 'value'],
    data: function () {
        return {
            reactiveValue: this.slugifyValue(this.value)
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

        EventBus.$on('input', function (parameters) {
            if (parameters.column.type == 'column' && parameters.column.column == vm.options.field) {
                vm.reactiveValue = vm.slugifyValue(parameters.value);
            }
        });
    },
    watch: {
        value: function (value) {
            this.reactiveValue = value;
        },
        reactiveValue: function (value) {
            this.$emit('input', this.slugifyValue(value));
        }
    }
};
</script>