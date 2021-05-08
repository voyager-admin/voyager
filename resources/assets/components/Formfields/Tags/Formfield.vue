<template>
    <slot v-if="action == 'query'"></slot>
    <div v-else-if="action == 'browse'">
        <badge v-for="(tag, i) in modelValue.slice(0, options.display_amount > 0 ? options.display_amount : modelValue.length)" :key="'tag-'+i">
            {{ tag }}
        </badge>
        <template v-if="options.display_amount > 0 && modelValue.length > options.display_amount">
            + {{ modelValue.length - options.display_amount }}
        </template>
    </div>
    <div v-else-if="action == 'edit' || action == 'add'">
        <tag-input
            v-model="value"
            :min="options.min"
            :max="options.max"
            :reorder="options.reorder"
            :duplicates="options.duplicates"
            :empty="options.empty"
        />
    </div>
    <div v-else>
        {{ modelValue }}
    </div>
</template>

<script>
import formfield from '../../../js/mixins/formfield';

export default {
    mixins: [formfield],
    computed: {
        value: {
            get() {
                if (!this.isArray(this.modelValue)) {
                    this.$emit('update:modelValue', []);
                    return [];
                }

                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        },
    },
}
</script>