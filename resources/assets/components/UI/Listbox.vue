<template>
    <div class="listbox input w-full" v-click-outside="close">
        <div @click="toggle" class="w-full cursor-pointer">
            <slot>
                <div v-if="$slots.default === null">
                    <span v-if="selectedOptions.length == 0">
                        {{ selectOptionText }}
                    </span>
                    <div v-else>
                        <div v-if="multiple">
                            <badge
                                color="accent"
                                v-for="(option, i) in selectedOptions"
                                :key="i"
                                icon="x"
                                @click-icon.prevent.stop="select(option)"
                            >
                                {{ option.value }}
                            </badge>
                        </div>
                        <span v-else>
                            {{ selectedOptions[0].value }}
                        </span>
                    </div>
                </div>
            </slot>
        </div>
        <collapse-transition class="options-container -m-3">
            <div v-show="isOpen" class="rounded-md py-1 max-h-128 text-base leading-6 shadow-xs overflow-auto focus:outline-none sm:text-sm sm:leading-5">
                <div>
                    <div v-if="search" class="px-1 pb-1">
                        <input class="input small w-full" :placeholder="searchOptionsText" v-model="query" @input="$emit('search', $event.target.value)" ref="search_input">
                    </div>
                    <div class="w-full flex justify-center text-xl my-2" v-if="loading">
                        {{ loadingText }}
                    </div>
                    <div
                        class="option"
                        v-for="(option, i) in options"
                        :key="i"
                        :class="[selected(option) ? 'selected' : '', disabled ? 'cursor-not-allowed' : 'cursor-pointer']"
                        @click="select(option)"
                    >
                        <div class="flex items-center space-x-3">
                            <span class="font-normal block truncate">
                                {{ option.value }}
                            </span>
                        </div>
                        <span v-show="selected(option)" class="check">
                            <icon icon="check" :size="5"></icon>
                        </span>
                    </div>
                    <div v-if="options.length == 0" class="w-full flex justify-center text-xl my-2">
                        {{ noResultsText }}
                    </div>
                    <div v-if="pages > 0" class="w-full py-1 pl-1">
                        <pagination
                            :page-count="pages"
                            v-model.number="currentPage"
                            :first-last-buttons="false"
                            small
                        />
                    </div>
                </div>
            </div>
        </collapse-transition>
    </div>
</template>

<script>
import closable from '../../js/mixins/closable';

export default {
    mixins: [closable],
    props: {
        options: {
            type: Array,
            required: true
        },
        value: {
            required: true
        },
        multiple: {
            type: Boolean,
            default: false,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        loading: {
            type: Boolean,
            default: false,
        },
        search: {
            type: Boolean,
            default: false,
        },
        searchOptionsText: {
            type: String,
            default: 'Search options',
        },
        selectOptionText: {
            type: String,
            default: 'Select an option',
        },
        noResultsText: {
            type: String,
            default: 'No results',
        },
        loadingText: {
            type: String,
            default: 'Loading...',
        },
        closeOnSelect: {
            type: Boolean,
            default: true,
        },
        pages: {
            type: Number,
            default: 0,
        }
    },
    data: function () {
        return {
            currentPage: 1,
            query: '',
        };
    },
    methods: {
        select: function (option) {
            if (this.disabled) {
                return;
            }
            if (this.multiple) {
                if (this.value.includes(option.key)) {
                    this.$emit('input', this.value.whereNot(option.key));
                } else {
                    this.$emit('input', [...this.value || [], option.key]);
                }
            } else {
                if (this.value == option.key) {
                    this.$emit('input', null);
                } else {
                    this.$emit('input', option.key);
                }
            }

            if (this.closeOnSelect) {
                this.close();
            }
        },
        selected: function (option) {
            if (this.value === null) {
                return false;
            }
            if (this.multiple) {
                return this.value.includes(option.key);
            }

            return this.value == option.key;
        },
        focusSearchInput: function () {
            var vm = this;
            Vue.nextTick(function () {
                if (vm.$refs.search_input) {
                    vm.$refs.search_input.focus();
                }
            });
        }
    },
    computed: {
        selectedOptions: function () {
            var vm = this;
            var selected = vm.value;
            if (!vm.isArray(selected)) {
                if (selected === null) {
                    return [];
                }
                selected = [selected];
            }

            return selected.map(function (option) {
                return vm.options.where('key', option)[0];
            });
        },
    },
    watch: {
        currentPage: function (page) {
            this.$emit('page', page);
            this.focusSearchInput();
        },
        isOpen: function () {
            this.focusSearchInput();
        },
        'options': {
            deep: true,
            handler: function () {
                this.focusSearchInput();
            }
        }
    }
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
            &:hover {
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
            @apply select-none relative py-2 pl-4 pr-8;
            .check {
                @include text-color(accent-text-color, 'colors.blue.500');
                @apply absolute inset-y-0 right-0 flex items-center pr-4;
            }
            &:hover {
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