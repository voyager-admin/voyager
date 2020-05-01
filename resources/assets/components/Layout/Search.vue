<template>
    <div v-click-outside="close">
        <input
            autocomplete="off"
            type="text"
            class="py-2 hidden sm:block text-lg appearance-none bg-transparent leading-normal w-full search focus:outline-none"
            v-model="query" @input="search" :placeholder="placeholder">
        <input
            autocomplete="off"
            type="text"
            class="py-2 block sm:hidden text-lg appearance-none bg-transparent leading-normal w-full search focus:outline-none"
            v-model="query" @input="search" :placeholder="mobilePlaceholder">
        <dropdown ref="results_dd" pos="right">
            <span v-for="(bread, table) in searchResults" :key="'bread-results-'+table">
                <h6 class="ml-3 mt-3">{{ translate($store.getBreadByTable(table).name_plural, true) }}</h6>
                <a v-for="(result, key) in bread.results" :key="'result-'+table+'-'+key" class="link" :href="getResultUrl(table, key)">
                    {{ result }}
                </a>
                <a :href="moreUrl(table)" v-if="bread.count > Object.keys(bread.results).length" class="link underline text-sm">
                    {{ __('voyager::generic.more_results', { num: (bread.count - Object.keys(bread.results).length)}) }}
                </a>
            </span>
        </dropdown>
    </div>
</template>
<script>
export default {
    props: ['placeholder', 'mobilePlaceholder'],
    data: function () {
        return {
            searchResults: {},
            query: '',
        };
    },
    watch: {
        query: function (query) {
            this.search(query);
        }
    },
    methods: {
        close: function () {
            this.$refs.results_dd.close();
            this.query = '';
        },
        search: debounce(function (e) {
            var vm = this;
            vm.searchResults = [];
            if (vm.query == '') {
                return;
            }

            axios.post(vm.route('voyager.globalsearch'), {
                query: vm.query,
            })
            .then(function (response) {
                vm.searchResults = response.data;
                if (Object.keys(vm.searchResults).length > 0) {
                    vm.$refs.results_dd.open();
                } else {
                    vm.$refs.results_dd.close();
                }
            })
            .catch(function (errors) {
                //
            });
            
        }, 250),
        moreUrl: function (table) {
            var bread = this.$store.getBreadByTable(table);

            return this.route('voyager.'+this.translate(bread.slug, true)+'.browse')+'?query='+this.query;
        },
        getResultUrl: function (table, key) {
            var bread = this.$store.getBreadByTable(table);

            return this.route('voyager.'+this.translate(bread.slug, true)+'.read', key);
        }
    },
    mounted: function () {
        var vm = this;
        document.body.addEventListener('keydown', event => {
            if (event.keyCode === 27) {
                vm.search('');
            }
        });
    },
};
</script>
<style lang="scss" scoped>
.voyager-search-results {
    @apply absolute bg-white text-black rounded-lg border-gray-600 p-8 origin-top-left;
}

.mode-dark .voyager-search-results {
    @apply bg-black text-white;
}
</style>