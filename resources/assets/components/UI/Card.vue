<template>
<div class="card border-default">
    <div class="header" v-if="!noHeader">
        <div class="flex flex-wrap justify-between">
            <slot name="title">
                <div class="flex space-x-2 items-center">
                    <icon v-if="icon" :icon="icon" :size="iconSize" />
                    <component :is="`h${titleSize}`" class="leading-6 font-medium" :class="titlePointer ? 'cursor-pointer' : ''" @click="$emit('click-title', $event)">
                        {{ title }}
                    </component>
                    <p class="mt-1 text-sm leading-5 text-gray-500">
                        {{ description }}
                    </p>
                </div>
            </slot>
            <div class="flex flex-wrap items-start">
                <slot name="actions"></slot>
            </div>
        </div>
    </div>
    <slot></slot>
</div>
</template>
<script>
export default {
    emits: ['click-title'],
    props: {
        noHeader: {
            type: Boolean,
            default: false,
        },
        title: {
            type: String,
            default: '',
        },
        titleSize: {
            type: Number,
            default: 4,
        },
        titlePointer: {
            type: Boolean,
            default: false,
        },
        icon: {
            type: String,
            default: null
        },
        iconSize: {
            type: Number,
            default: 6
        },
        description: {
            type: String,
            default: '',
        },
    },
};
</script>

<style lang="scss">
@import "../../sass/mixins/bg-color";
@import "../../sass/mixins/border-color";
@import "../../sass/mixins/text-color";

.dark .card {
    @include bg-color(card-bg-color-dark, 'colors.gray.800');
    @include text-color(card-text-color-dark, 'colors.gray.300');

    &.border-default {
        @include border-color(card-border-color-dark, 'colors.gray.700');
    }

    .header {
        @include border-color(card-border-color-dark, 'colors.gray.700');

        h3 {
            @include text-color(card-title-color-dark, 'colors.gray.300');
        }
    }
}

.card {
    @apply shadow border rounded-lg p-4 mx-1 mb-4;
    @include bg-color(card-bg-color, 'colors.white');
    @include text-color(card-text-color, 'colors.gray.700');

    &.border-default {
        @include border-color(card-border-color, 'colors.gray.400');
    }
    .header {
        @apply p-2;
        @include border-color(card-border-color, 'colors.gray.400');

        h3 {
            @include text-color(card-title-color, 'colors.gray.700');
        }
    }
}
</style>