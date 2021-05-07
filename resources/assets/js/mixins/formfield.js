export default {
    emits: ['update:modelValue'],
    props: {
        action: {
            type: String,
            required: true,
            validator: function (value) {
                return ['query', 'browse', 'read', 'edit', 'add'].indexOf(value) >= 0;
            }
        },
        modelValue: {
            required: true,
        },
        column: {
            type: Object,
            required: true,
        },
        options: {
            type: Object,
            required: true,
        },
        translatable: {
            type: Boolean,
            default: false,
        },
        relationships: {
            type: Array,
            default: () => [],
        },
        fromRelationship: {
            type: Boolean,
            default: false,
        },
        primaryKey: {
            default: null,
        },
        placeholder: {
            type: String,
            default: '',
        },
        errors: {
            type: Array,
            default: () => []
        }
    },
};