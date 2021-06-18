<template>
    <slot v-if="action == 'query'"></slot>
    <template v-else-if="action == 'browse'">
        <span v-if="options.display_length > 0">
            {{ String(modelValue).slice(0, options.display_length) }}
        </span>
        <span v-else>
            {{ modelValue }}
        </span>
    </template>
    <template v-else-if="action == 'edit' || action == 'add'">
        <input
            type="text"
            class="input w-full"
            :class="options.classes"
            :value="modelValue || translate(options.default_value, true)"
            @input="$emit('update:modelValue', slugValue($event.target.value, false))"
            :placeholder="translate(options.placeholder, true)">
    </template>
    <template v-else>
        {{ modelValue }}
    </template>
</template>

<script>
import formfield from '@mixins/formfield';

export default {
    mixins: [formfield],
    created() {
        this.$eventbus.on('input', (payload) => {
            if (payload.column.type == 'column' && payload.column.column == this.options.column) {
                let slugged = this.slugValue(payload.value, this.options.strict);
                if (this.modelValue !== slugged) {
                    this.$emit('update:modelValue', slugged);
                }
            }
        });
    },
    methods: {
        slugValue(value, strict) {
            return this.slugify(value, {
                strict: strict || true,
                lower: this.options.lower || true,
                replacement: this.options.replacement || '-'
            });
        }
    },
}
</script>