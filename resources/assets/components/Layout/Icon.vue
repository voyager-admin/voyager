<template>
    <i :class="`h-${size} w-${size}`" @click="$emit('click', $event)" v-html="iconContent"></i>
</template>

<script>
import icons from '../../js/icons';

export default {
    emits: ['click'],
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
        iconContent() {
            let icon;

            if (this.icon == 'helm') {
                icon = require(`../../svg/helm.svg`);
            } else if (this.icon == 'bread') {
                icon = require(`../../svg/bread.svg`);
            } else if (icons.includes(this.icon)) {
                icon = require(`../../../../node_modules/heroicons/outline/${this.icon}.svg`);
            } else {
                console.warn(`Icon "${this.icon}" does not exist`);
                icon = require(`../../../../node_modules/heroicons/outline/ban.svg`);
            }
            
            if (icon.hasOwnProperty('default')) {
                return icon.default;
            }

            return icon;
        }
    },
    mounted() {
        //this.$el.firstChild.classList.add(`h-${this.size}`);
        //this.$el.firstChild.classList.add(`w-${this.size}`);
        //console.log(this.$el);
    }
};
</script>