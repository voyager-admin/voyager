<template>
    <div class="w-full" v-click-outside="close">
        <dropdown ref="results_dd" pos="right" :width="'full'" class="w-full" dont-open-on-click>
            <div class="grid p-2" :class="gridClasses">
                <div v-for="(bread, table) in searchResults" :key="'bread-results-'+table">
                    <h6 class="ml-3 mt-3">{{ translate($store.getBreadByTable(table).name_plural, true) }}</h6>
                    <a v-for="(result, key) in bread.results" :key="'result-'+table+'-'+key" class="link rounded-md" :href="getResultUrl(table, key)">
                        {{ translate(result, true) }}
                    </a>
                    <a :href="moreUrl(table)" v-if="bread.count > Object.keys(bread.results).length" class="link underline text-sm rounded-md">
                        {{ __('voyager::generic.more_results', { num: (bread.count - Object.keys(bread.results).length)}) }}
                    </a>
                </div>
            </div>
            <div slot="opener">
                <input
                    autocomplete="off"
                    type="text"
                    class="py-2 hidden sm:block text-lg appearance-none bg-transparent leading-normal w-full focus:outline-none"
                    @dblclick="query = ''"
                    @keydown.esc="query = ''"
                    v-model="query" @input="search" :placeholder="placeholder"
                >
                <input
                    autocomplete="off"
                    type="text"
                    class="py-2 block sm:hidden text-lg appearance-none bg-transparent leading-normal w-full focus:outline-none"
                    v-model="query" @input="search" :placeholder="mobilePlaceholder"
                >
            </div>
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
            if (query === '' || query === null) {
                this.$refs.results_dd.close();
            }
            this.search(query);
        }
    },
    computed: {
        gridClasses: function () {
            return [
                'grid-cols-' + this.max(1),
                'md:grid-cols-' + this.max(2),
                'lg:grid-cols-' + this.max(3),
                'xl:grid-cols-' + this.max(4)
            ];
        }
    },
    methods: {
        max: function (val) {
            if (Object.keys(this.searchResults).length < val) {
                return Object.keys(this.searchResults).length;
            }

            return val;
        },
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

            return this.route('voyager.'+this.translate(bread.slug, true)+'.browse')+'?global='+this.query;
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

.dark .voyager-search-results {
    @apply bg-black text-white;
}
</style>