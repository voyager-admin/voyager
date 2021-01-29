<template>
    <div>
        <input type="text" class="input w-full mb-3" :placeholder="__('voyager::generic.search_icons')" v-model="query" />
        <div class="grid grid-cols-6 gap-1">
            <tooltip v-for="(icon, i) in filteredIcons.slice(start, end)" :key="'icon-' + i" :value="icon.readable">
                <button
                    class="button justify-center my-1 w-full"
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
    data() {
        return {
            query: '',
            page: 0,
            resultsPerPage: 60,
        };
    },
    methods: {
        selectIcon(icon) {
            this.$emit('select', icon);
        },
    },
    computed: {
        start() {
            return this.page * this.resultsPerPage;
        },
        end() {
            return this.start + this.resultsPerPage;
        },
        pages() {
            return Math.ceil(this.filteredIcons.length / this.resultsPerPage);
        },
        filteredIcons() {
            var q = this.query.toLowerCase();
            return icons.whereLike(q).map((icon) => {
                return {
                    name: this.kebabCase(icon),
                    readable: icon,
                }
            });
        },
    },
    created() {
        this.$watch(() => this.query, () => {
            this.page = 0;
        });
    },
};
</script>