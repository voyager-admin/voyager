<template>
<div
    class="select"
    v-click-outside="close"
    @keydown.up.prevent.stop="focusPrevious"
    @keydown.down.prevent.stop="focusNext"
    @keydown.esc.prevent.stop="close"
    @keydown.enter.prevent.stop="onKeyEnter"
    @keydown="$refs.search.focus()"
    @scroll.prevent.stop="">
    <span class="inline-block w-full rounded-md shadow-sm">
        <button @click="toggle()" type="button" class="voyager-input w-full">
            <div class="flex items-center space-x-3">
                <span class="block truncate" v-if="!isArray(value)">
                    {{ getValueByKey(value) }}
                </span>
                <span v-else-if="isArray(value) && value.length > 0">
                    <badge v-for="key in value" :key="key" :color="color" icon="x" @click-icon.prevent.stop="selectOption(key)">
                        {{ getValueByKey(key) }}
                    </badge>
                </span>
                <span v-else>
                    {{ selectOptionText }}
                </span>
            </div>
        </button>
    </span>
    <collapse-transition v-show="isOpen" class="options-container">
        <ul class="rounded-md py-1 max-h-128 text-base leading-6 shadow-xs overflow-auto focus:outline-none sm:text-sm sm:leading-5">
            <li v-if="searchable" class="p-2">
                <input type="text" class="voyager-input w-full" :placeholder="searchText" v-model="searchQuery" ref="search">
            </li>
            <li v-if="filteredOptions.length == 0 || (dynamic && options.length == 0)" class="option">
                {{ noOptionsFoundText }}
            </li>
            <li v-if="dynamic && loading" class="option">
                {{ loadingText }}
            </li>
            <li
                v-for="(option, i) in filteredOptions"
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
            default: false,
        },
        color: {
            type: String,
            default: 'blue',
        },
        searchable: {
            type: Boolean,
            default: true,
        },
        searchText: {
            type: String,
            default: 'Search for an option',
        },
        noOptionsFoundText: {
            type: String,
            default: 'No options found 😢',
        },
        dynamic: {
            type: Boolean,
            default: false,
        },
        loading: {
            type: Boolean,
            default: true,
        },
        loadingText: {
            type: String,
            default: 'Loading',
        }
    },
    data: function () {
        return {
            isOpen: false,
            focused: null,
            searchQuery: '',
        };
    },
    computed: {
        filteredOptions: function () {
            var vm = this;

            return vm.options.filter(function (option) {
                return option.value.toUpperCase().includes(vm.searchQuery.toUpperCase()) && !vm.dynamic;
            });
        }
    },
    watch: {
        searchQuery: function (query) {
            this.$emit('search', query);
        }
    },
    methods: {
        open: function () {
            this.open = true;
            this.searchQuery = '';
            this.$emit('opened');
        },
        close: function () {
            this.isOpen = false;
            this.searchQuery = '';
            this.$emit('closed');
        },
        toggle: function () {
            this.isOpen = !this.isOpen;
            this.$emit(this.isOpen ? 'opened' : 'closed');
        },
        onKeyEnter: function () {
            if (!this.isOpen) {
                this.open();
            } else if (this.focused !== null) {
                this.selectOption(this.filteredOptions[this.focused].key);
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
                    this.focused = this.filteredOptions.length - 1;
                }
            } else {
                this.focused = this.filteredOptions.length - 1;
            }
            if (!this.isOpen && !this.multiple) {
                this.$emit('input', this.filteredOptions[this.focused].key);
            }
        },
        focusNext: function () {
            if (this.focused !== null && this.focused < (this.filteredOptions.length - 1)) {
                this.focused = this.focused + 1;
            } else {
                this.focused = 0;
            }
            if (!this.isOpen && !this.multiple) {
                this.$emit('input', this.filteredOptions[this.focused].key);
            }
        },
        isSelected: function (key) {
            if (this.isArray(this.value) && this.multiple) {
                return this.value.includes(key);
            }

            return this.value == key;
        },
        getValueByKey: function (key) {
            var option = this.filteredOptions.filter(function (option) {
                return option.key == key;
            })[0];

            return option ? option.value : this.selectOptionText;
        },
    },
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
                @include bg-color(select-bg-focused-color-dark, 'colors.gray.700');
            }
            &.selected {
                @include text-color(select-text-selected-color-dark, 'colors.gray.100');
                @include bg-color(select-bg-selected-color-dark, 'colors.gray.750');
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