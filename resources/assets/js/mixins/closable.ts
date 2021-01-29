import { watch, defineComponent } from 'vue';

export default defineComponent ({
    emits: ['opened', 'closed', 'toggled'],
    data() {
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
        watch(() => this.isOpen, (open: boolean) => {
                if (open) {
                    this.$emit('opened');
                } else {
                    this.$emit('closed');
                }
    
                this.$emit('toggled');
            }
        );
    },
});