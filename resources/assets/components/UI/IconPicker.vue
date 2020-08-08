<template>
    <div>
        <input type="text" class="input w-full mb-3" :placeholder="__('voyager::generic.search_icons')" v-model="query" />
        <div class="grid grid-cols-12 gap-1">
            <button
                class="button accent justify-center my-1"
                v-for="(icon, i) in filteredIcons.slice(start, end)"
                :key="'icon-' + i"
                @dblclick="$emit('select', icon.name)"
                v-tooltip="icon.readable">
                <icon :icon="icon.name" :size="6" />
            </button>
        </div>
        <pagination class="mt-2" :page-count="pages" v-on:input="page = $event - 1" v-bind:value="page + 1" :first-last-buttons="false" :prev-next-buttons="false"></pagination>
    </div>
</template>
<script>
import icons from '../../js/icons';

export default {
    data: function () {
        return {
            query: '',
            page: 0,
            resultsPerPage: 120,
        };
    },
    methods: {
        selectIcon: function (icon) {
            this.$emit('select', icon);
        },
    },
    computed: {
        start: function () {
            return this.page * this.resultsPerPage;
        },
        end: function () {
            return this.start + this.resultsPerPage;
        },
        pages: function () {
            return Math.ceil(this.filteredIcons.length / this.resultsPerPage);
        },
        filteredIcons: function () {
            var vm = this;
            var q = vm.query.toLowerCase();
            return icons.whereLike(q).map(function (icon) {
                return {
                    name: vm.kebab_case(icon),
                    readable: icon,
                }
            });
        },
    },
    watch: {
        query: function (q) {
            this.page = 0;
        }
    }
};
</script>