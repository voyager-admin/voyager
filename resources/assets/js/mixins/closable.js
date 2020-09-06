export default {
    emits: ['opened', 'closed', 'toggled'],
    data: function () {
        return {
            isOpen: false,
        }
    },
    methods: {
        open: function () {
            this.isOpen = true;
        },
        close: function () {
            this.isOpen = false;
        },
        toggle: function () {
            this.isOpen = !this.isOpen;
        }
    },
    watch: {
        isOpen: function (open) {
            if (open) {
                this.$emit('opened');
            } else {
                this.$emit('closed');
            }

            this.$emit('toggled');
        }
    }
};