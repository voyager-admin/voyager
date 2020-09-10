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
    created: function () {
        var vm = this;
        vm.$watch(
            () => this.isOpen,
            function (open) {
                if (open) {
                    vm.$emit('opened');
                } else {
                    vm.$emit('closed');
                }
    
                vm.$emit('toggled');
            }
        );
    },
};