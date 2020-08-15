<template>
    <div class="w-full">
        <modal ref="results_modal" :title="placeholder" icon="search">
            <div>
                <input
                    autocomplete="off"
                    type="text"
                    class="input w-full mb-2"
                    @dblclick="query = ''"
                    @keydown.esc="query = ''"
                    v-model="query" @input="search" :placeholder="placeholder"
                    ref="search_input"
                >
                <div v-if="query === null || query === ''">
                    <h5>{{ __('voyager::generic.enter_query') }}</h5>
                </div>
                <div v-else-if="loading">
                    <h5>{{ __('voyager::generic.loading_please_wait') }}</h5>
                </div>
                <div v-else-if="Object.keys(searchResults).length == 0">
                    <h5>{{ __('voyager::generic.no_results') }}</h5>
                </div>
                <div class="grid" :class="gridClasses" v-else>
                    <div v-for="(bread, table) in searchResults" :key="'bread-results-'+table" class="w-full">
                        <h5>{{ translate($store.getBreadByTable(table).name_plural, true) }}</h5>
                        <p v-for="(result, key) in bread.results" :key="'result-'+table+'-'+key">
                            <a :href="getResultUrl(table, key)">
                                {{ translate(result, true) }}
                            </a>
                        </p>
                        <a :href="moreUrl(table)" v-if="bread.count > Object.keys(bread.results).length" class="link underline text-sm rounded-md">
                            {{ __('voyager::generic.more_results', { num: (bread.count - Object.keys(bread.results).length)}) }}
                        </a>
                    </div>
                </div>
            </div>
        </modal>
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
</template>
<script>
export default {
    props: ['placeholder', 'mobilePlaceholder'],
    data: function () {
        return {
            searchResults: {},
            query: '',
            loading: false,
            no_result_notification: null,
        };
    },
    watch: {
        query: function (query) {
            var vm = this;
            vm.loading = true;
            vm.$refs.results_modal.open();
            Vue.nextTick(function () {
                vm.$refs.search_input.focus();
            });
            vm.search(query);
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
        search: debounce(function (e) {
            var vm = this;
            vm.searchResults = [];
            if (vm.query == '') {
                return;
            }

            vm.loading = true;

            axios.post(vm.route('voyager.globalsearch'), {
                query: vm.query,
            })
            .then(function (response) {
                vm.searchResults = response.data;
            })
            .catch(function (errors) {
                // TODO: ...
            })
            .then(function () {
                vm.loading = false;
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