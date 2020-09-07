<template>
    <div>
        <input type="text" class="input w-full mb-3" :placeholder="__('voyager::generic.search_icons')" v-model="query" />
        <div class="grid grid-cols-12 gap-1">
            <tooltip v-for="(icon, i) in filteredIcons.slice(start, end)" :key="'icon-' + i" :value="icon.readable">
                <button
                    class="button justify-center my-1"
                    
                    @dblclick="$emit('select', icon.name)">
                    <icon :icon="icon.name" :size="6" />
                </button>
            </tooltip>
        </div>
        <pagination class="mt-2" :page-count="pages" @update:model-value="page = $event - 1" :model-value="page + 1" :first-last-buttons="false" :prev-next-buttons="false"></pagination>
    </div>
</template>
<script>
import icons from '../../js/icons';

export default {
    emits: ['select'],
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