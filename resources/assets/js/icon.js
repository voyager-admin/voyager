import { h, watch } from 'vue';
import xIcon from '@heroicons/vue/outline/XIcon';
import breadIcon from '@/icons/bread';
import helmIcon from '@/icons/helm';
import strings from '@mixins/strings';

export default {
    props: {
        icon: {
            type: String,
            required: true
        },
        size: {
            type: [String, Number],
            default: 5,
        },
        transitionSize: {
            type: Number,
            default: 0,
        },
    },
    render() {
        return h('i', { class: `h-${this.currentSize} w-${this.size} transition-width duration-500`, key: `icon-${this.icon}` }, [
            this.getIcon()
        ]);
    },
    methods: {
        getIcon() {
            if (Array.isArray(window.icons)) {
                let name = strings.methods.studly(this.icon);
                if (window.icons[name]) {
                    return window.icons[name]();
                } else if (name == 'Bread') {
                    return breadIcon();
                } else if (name == 'Helm') {
                    return helmIcon();
                } else {
                    console.warn(`The icon "${name}" does not exist`);
                }
            }

            return xIcon();
        }
    },
    computed: {
        currentSize() {
            return this.transitionSize > 0 ? this.transitionSize : this.size;
        }
    },
    mounted() {
        if (window.icons === undefined) {
            console.error('No icons were registered. Please make sure your icon-pack injects an array of icons into `window.icons`');
        }

        if (this.$el.firstChild) {
            this.$el.firstChild.classList.remove(...this.$el.firstChild.classList);
            
            this.$el.firstChild.classList.add(`w-full`);
            this.$el.firstChild.classList.add(`h-full`);
        }
    }
};