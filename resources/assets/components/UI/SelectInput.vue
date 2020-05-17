<template>
<div class="select" v-click-outside="close" @keydown.stop.prevent="onKeyDown">
    <span class="inline-block w-full rounded-md shadow-sm">
        <button @click="toggle()" type="button" class="voyager-input w-full">
            <div class="flex items-center space-x-3">
                <span class="block truncate" v-if="!isArray(value)">
                    {{ getValueByKey(value) }}
                </span>
                <span v-else-if="isArray(value) && value.length > 0">
                    <badge v-for="key in value" :key="key" :color="color" icon="times" @click-icon.prevent.stop="selectOption(key)">
                        {{ getValueByKey(key) }}
                    </badge>
                </span>
                <span v-else>
                    {{ selectOptionText }}
                </span>
            </div>
            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <icon icon="direction" :size="5"></icon>
            </span>
        </button>
    </span>
    <collapse-transition v-show="isOpen" class="options-container">
        <ul class="rounded-md py-1 max-h-128 text-base leading-6 shadow-xs overflow-auto focus:outline-none sm:text-sm sm:leading-5">
            <li
                v-for="(option, i) in options"
                :key="'option-'+option.key"
                class="option"
                :class="[focused == i ? 'focused' : '', isSelected(option.key) ? 'selected' : '']"
                @click="selectOption(option.key)"
                @mouseover="focused = i">
                <div class="flex items-center space-x-3">
                    <icon v-if="option.icon" :icon="option.icon" :size="6" class="flex-shrink-0"></icon>
                    <span :class="[isSelected(option.key) ? 'font-semibold' : 'font-normal']" class="block truncate">
                        {{ option.value }}
                    </span>
                </div>
                <span v-show="isSelected(option.key)" class="check">
                    <icon icon="check" :size="5"></icon>
                </span>
            </li>
        </ul>
    </collapse-transition>
</div>
</template>
<script>
export default {
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
        multiple: {
            type: Boolean,
            default: true,
        },
        color: {
            type: String,
            default: 'blue',
        }
    },
    data: function () {
        return {
            isOpen: false,
            focused: null
        };
    },
    methods: {
        open: function () {
            this.open = true;
            this.$emit('opened');
        },
        close: function () {
            this.isOpen = false;
            this.$emit('closed');
        },
        toggle: function () {
            this.isOpen = !this.isOpen;
            this.$emit(this.isOpen ? 'opened' : 'closed');
        },
        onKeyDown: function (e) {
            if (e.key == 'Escape') {
                this.close();
            } else if (e.key == 'ArrowUp') {
                this.focusPrevious();
            } else if (e.key == 'ArrowDown') {
                this.focusNext();
            } else if (e.key == 'Enter') {
                if (!this.isOpen) {
                    this.open();
                } else if (this.focused !== null) {
                    this.selectOption(this.options[this.focused].key);
                }
            }
        },
        selectOption: function (key) {
            if (this.isArray(this.value) && this.multiple) {
                var array = this.value;
                if (this.value.includes(key)) {
                    array.splice(array.indexOf(key), 1);
                } else {
                    array.push(key);
                }
                this.$emit('input', array);
            } else {
                this.$emit('input', key);
            }
            if (this.closeOnSelect) {
                this.close();
            }
        },
        focusPrevious: function () {
            if (this.focused !== null) {
                if (this.focused > 0) {
                    this.focused = this.focused - 1;
                } else {
                    this.focused = this.options.length - 1;
                }
            } else {
                this.focused = this.options.length - 1;
            }
            if (!this.isOpen) {
                this.$emit('input', this.options[this.focused].key);
            }
        },
        focusNext: function () {
            if (this.focused !== null && this.focused < (this.options.length - 1)) {
                this.focused = this.focused + 1;
            } else {
                this.focused = 0;
            }
            if (!this.isOpen) {
                this.$emit('input', this.options[this.focused].key);
            }
        },
        isSelected: function (key) {
            if (this.isArray(this.value) && this.multiple) {
                return this.value.includes(key);
            }

            return this.value == key;
        },
        getValueByKey: function (key) {
            var option = this.options.filter(function (option) {
                return option.key == key;
            })[0];

            return option ? option.value : this.selectOptionText;
        }
    },
    computed: {
        
    }
};
</script>

<style lang="scss" scoped>
@import "../../sass/mixins/bg-color";
@import "../../sass/mixins/border-color";
@import "../../sass/mixins/text-color";

.mode-dark .select {
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
                @include bg-color(select-bg-focused-color-dark, 'colors.gray.750');
            }
            &.selected {
                @include text-color(select-text-selected-color-dark, 'colors.gray.100');
                @include bg-color(select-bg-selected-color-dark, 'colors.gray.700');
            }
        }
    }
}

.select {
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
                @include bg-color(select-bg-focused-color, 'colors.gray.100');
            }
            &.selected {
                @include text-color(select-text-selected-color, 'colors.gray.900');
                @include bg-color(select-bg-selected-color, 'colors.gray.150');
            }
        }
    }
}
</style>