<template>
    <component :is="iconName" v-bind="$props" :class="classes"></component>
</template>

<script>
import icons from '@/icons';

export default {
    props: {
        icon: {
            type: String,
        },
        size: {
            type: Number,
            default: 5
        },
        transitionSize: {
            type: Number,
            default: 0
        }
    },
    components: icons,
    computed: {
        iconName() {
            let name = this.studly(this.icon);
            if (this.$voyager.componentExists(`${this.icon}Icon`)) {
                return `${this.icon}Icon`;
            } else if (Object.keys(icons).includes(name)) {
                return name;
            }

            console.warn(`Icon "${this.icon}" does not exist!`);

            return 'X';
        },
        classes() {
            return this.$props.class + ` transition-width duration-500 h-${this.currentSize} w-${this.size}`;
        },
        currentSize() {
            return this.transitionSize > 0 ? this.transitionSize : this.size;
        }
    }
}
</script>
