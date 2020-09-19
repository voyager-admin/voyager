<template>
    <slot v-if="action == 'query'"></slot>
    <div v-else-if="action == 'browse'">
        <span v-if="options.display_length > 0">
            {{ String(modelValue).slice(0, options.display_length) }}
        </span>
        <span v-else>
            {{ modelValue }}
        </span>
    </div>
    <div v-else-if="action == 'edit' || action == 'add'">
        <input
            type="text"
            class="input w-full"
            :value="modelValue || translate(options.default_value, true)"
            @input="$emit('update:modelValue', slugValue($event.target.value, false))"
            :placeholder="translate(options.placeholder, true)">
    </div>
    <div v-else>
        {{ modelValue }}
    </div>
</template>

<script>
import formfield from '../../../js/mixins/formfield';

export default {
    mixins: [formfield],
    created: function () {
        var vm = this;
        vm.$eventbus.on('input', function (payload) {
            // TODO: We could consider other column types as well
            if (payload.column.type == 'column' && payload.column.column == vm.options.column) {
                vm.$emit('update:modelValue', vm.slugValue(payload.value, vm.options.strict));
            }
        });
    },
    methods: {
        slugValue: function (value, strict) {
            return this.slugify(value, {
                strict: strict || true,
                lower: this.options.lower || true,
                replacement: this.options.replacement || '-'
            });
        }
    },
}
</script>