<template>
    <Listbox v-bind:value="value" v-on:input="$emit('input', $event)" v-slot="{ isOpen }" class="listbox input" :close-on-select="closeOnSelect">
        <ListboxButton class="w-full inline-flex items-center" style="outline: none !important;">
            <span class="flex-grow text-left" v-if="!isArray(value) && value">{{ value }}</span>
            <span class="flex-grow text-left" v-else-if="(isArray(value) && value.length == 0) || !value">{{ selectOptionText }}</span>
            <div v-else-if="isArray(value)" class="flex-grow text-left">
                <badge icon="x" v-for="(val, i) in value" :key="i" @click-icon.prevent.stop="$emit('input', value.whereNot(val))" class="large">
                    {{ val }}
                </badge>
            </div>
            <icon class="flex-grow-0 ltr:ml-2 rtl:mr-2" icon="selector"></icon>
        </ListboxButton>
        <collapse-transition class="options-container -m-3">
            <ListboxList v-show="isOpen" class="rounded-md py-1 max-h-128 text-base leading-6 shadow-xs overflow-auto focus:outline-none sm:text-sm sm:leading-5">
                <ListboxOption
                    v-for="(option, i) in options"
                    :key="i"
                    :value="option.value"
                    v-slot="{ isActive, isSelected }"
                >
                    <div class="option" :class="[isActive ? 'focused' : '', isSelected ? 'selected' : '']">
                        <div class="flex items-center space-x-3">
                            <span :class="[isSelected ? 'font-semibold' : 'font-normal']" class="block truncate">
                                {{ option.value }}
                            </span>
                        </div>
                        <span v-show="isSelected" class="check">
                            <icon icon="check" :size="5"></icon>
                        </span>
                    </div>
                </ListboxOption>
            </ListboxList>
        </collapse-transition>
    </Listbox>
</template>

<script>
// TODO: This can be replaced once @tailwindui/vue does support multiple values
import {
    Listbox,
    ListboxButton,
    ListboxList,
    ListboxOption
} from '../../js/listbox';

export default {
    components: {
        Listbox,
        ListboxButton,
        ListboxList,
        ListboxOption
    },
    props: {
        options: {
            type: Array,
            required: true
        },
        value: {
            required: true
        },
        selectOptionText: {
            type: String,
            default: 'Select an option',
        },
        closeOnSelect: {
            type: Boolean,
            default: true,
        },
    },
};
</script>

<style lang="scss" scoped>
@import "../../sass/mixins/bg-color";
@import "../../sass/mixins/border-color";
@import "../../sass/mixins/text-color";
.dark .listbox {
    .options-container {
        @include bg-color(select-bg-color-dark, 'colors.gray.800');
        @include border-color(select-bg-color-dark, 'colors.gray.600');
        .option {
            @include text-color(select-text-color-dark, 'colors.gray.100');
            .check {
                @include text-color(accent-text-color-dark, 'colors.blue.500');
            }
            &.focused {
                @include text-color(select-text-focused-color-dark, 'colors.gray.100');
                @include bg-color(select-bg-focused-color-dark, 'colors.gray.700');
            }
            &.selected {
                @include text-color(select-text-selected-color-dark, 'colors.gray.100');
                @include bg-color(select-bg-selected-color-dark, 'colors.gray.750');
            }
        }
    }
}
.listbox {
    @apply relative text-base;
    .options-container {
        @apply absolute mt-1 w-full rounded-md shadow-lg z-50 border;
        @include bg-color(select-bg-color, 'colors.white');
        @include border-color(select-bg-color, 'colors.gray.400');
        .option {
            @include text-color(select-text-color, 'colors.gray.900');
            @apply cursor-pointer select-none relative py-2 pl-4 pr-8;
            .check {
                @include text-color(accent-text-color, 'colors.blue.500');
                @apply absolute inset-y-0 right-0 flex items-center pr-4;
            }
            &.focused {
                @include text-color(select-text-focused-color, 'colors.gray.900');
                @include bg-color(select-bg-focused-color, 'colors.gray.150');
            }
            &.selected {
                @include text-color(select-text-selected-color, 'colors.gray.900');
                @include bg-color(select-bg-selected-color, 'colors.gray.100');
            }
        }
    }
}
</style>