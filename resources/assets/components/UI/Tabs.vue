<template>
    <card class="tabs" dont-show-header>
        <div class="sm:hidden">
            <select class="input" @change="openByIndex($event.target.value)">
                <option v-for="(tab, i) in tabs" :key="'option-'+i" :value="i" class="capitalize">
                    {{ tab.title }}
                </option>
            </select>
        </div>
        <div class="hidden sm:block">
            <div class="border-b wrapper">
                <nav class="-mb-px flex">
                    <a href="#" @click.prevent="openByIndex(i)" class="tab" v-for="(tab, i) in tabs" :key="'tab-'+i" :class="[i > 0 ? 'ml-8' : '', currentTab == i ? 'active' : '']">
                        {{ tab.title }}
                    </a>
                </nav>
            </div>
        </div>
        <div class="content">
            <div v-for="(tab, i) in tabs" :key="'slot-'+i">
                <slot :name="tab.name" v-if="currentTab == i" />
            </div>
        </div>
    </card>
</template>
<script>
export default {
    emits: ['select'],
    props: {
        openTab: {
            type: String,
            default: null,
        },
        tabs: {
            type: Array,
            default: function () {
                return []
            }
        }
    },
    data: function () {
        return {
            currentTab: 0,
        };
    },
    methods: {
        openByIndex: function (index) {
            this.$emit('select', index);
            this.currentTab = index;
        },
        openByName: function (name) {
            var vm = this;
            vm.tabs.forEach(function (tab, i) {
                if (tab.name == name) {
                    vm.currentTab = i;
                }
            });
        }
    },
    computed: {
        current: function () {
            return this.tabs[this.currentTab];
        },
    },
    mounted: function () {
        if (this.openTab !== null) {
            this.openByName(this.openTab);
        }
    },
};
</script>

<style lang="scss" scoped>
@import "../../sass/mixins/bg-color";
@import "../../sass/mixins/border-color";
@import "../../sass/mixins/text-color";

.dark .tabs {
    .wrapper {
        @include border-color(tabs-border-color-dark, 'colors.gray.700');
        .tab {
            @include text-color(tabs-text-color-dark, 'colors.gray.400');
            &:hover {
                @include text-color(tabs-text-color-hover-dark, 'colors.gray.500');
                @include border-color(tabs-border-color-hover-dark, 'colors.gray.500');
            }
    
            &.active {
                @include text-color(tabs-text-color-active-dark, 'colors.blue.600');
                @include border-color(tabs-border-color-active-dark, 'colors.blue.500');
            }
        }
    }
}

.tabs {
    .wrapper {
        @include border-color(tabs-border-color, 'colors.gray.200');
        .tab {
            @apply whitespace-no-wrap py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 capitalize;
            @include text-color(tabs-text-color, 'colors.gray.400');
            &:hover {
                @include text-color(tabs-text-color-hover, 'colors.gray.700');
                @include border-color(tabs-border-color-hover, 'colors.gray.300');
            }
    
            &.active {
               @include text-color(tabs-text-color-active, 'colors.blue.600');
                @include border-color(tabs-border-color-active, 'colors.blue.500');
            }
        }
    }
    .content {
        @apply mt-4;
    }
}
</style>