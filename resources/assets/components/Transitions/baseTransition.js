export default {
    props: {
        group: {
            type: Boolean,
            default: false,
        },
        duration: {
            type: [Number, Object],
            default: 300,
        },
        tag: {
            type: String,
            default: 'div'
        },
        class: {
            type: String,
            default: ''
        }
    },
    computed: {
        componentType: function () {
            return this.group ? 'transition-group' : 'transition'
        }
    }
};