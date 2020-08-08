<template>
    <i :class="`h-${size} w-${size}`" @click="$emit('click', $event)" v-html="iconContent"></i>
</template>

<script>
import icons from '../../js/icons';

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
    },
    computed: {
        iconContent: function () {
            if (this.icon == 'helm') {
                return require(`../../svg/helm.svg`);
            } else if (this.icon == 'bread') {
                return require(`../../svg/bread.svg`);
            }

            if (icons.includes(this.icon)) {
                return require(`../../../../node_modules/heroicons/outline/${this.icon}.svg`);
            }
            console.warn(`Icon "${this.icon}" does not exist`);

            return require(`../../../../node_modules/heroicons/outline/ban.svg`);
        }
    },
    mounted: function () {
        this.$el.firstChild.classList.add(`h-${this.size}`);
        this.$el.firstChild.classList.add(`w-${this.size}`);
    }
};
</script>