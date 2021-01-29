<template>
    <div class="w-full" @keydown.esc="search('')">
        <modal ref="results_modal" :title="placeholder" icon="search" @closed="query = ''">
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
import { nextTick } from 'vue';
import wretch from '../../js/wretch';
import debounce from 'debounce';

export default {
    props: ['placeholder', 'mobilePlaceholder'],
    data() {
        return {
            searchResults: {},
            query: '',
            loading: false,
            no_result_notification: null,
        };
    },
    computed: {
        gridClasses() {
            return [
                'grid-cols-' + this.max(1),
                'md:grid-cols-' + this.max(2),
                'lg:grid-cols-' + this.max(3),
                'xl:grid-cols-' + this.max(4)
            ];
        }
    },
    methods: {
        max(val) {
            if (Object.keys(this.searchResults).length < val) {
                return Object.keys(this.searchResults).length;
            }

            return val;
        },
        search: debounce(function (e) {
            var vm = this;
            vm.searchResults = {};
            if (vm.query == '') {
                return;
            }

            vm.loading = true;

            wretch(vm.route('voyager.globalsearch'))
            .post({
                query: this.query,
            })
            .json((response) => {
                vm.searchResults = response;
            })
            .catch((response) => {
                vm.$store.handleAjaxError(response);
            })
            .then(() => {
                vm.loading = false;
            });
            
        }, 250),
        moreUrl(table) {
            var bread = this.$store.getBreadByTable(table);

            return this.route('voyager.'+this.translate(bread.slug, true)+'.browse')+'?global='+this.query;
        },
        getResultUrl(table, key) {
            var bread = this.$store.getBreadByTable(table);

            return this.route('voyager.'+this.translate(bread.slug, true)+'.read', key);
        }
    },
    created() {
        this.$watch(() => this.query, (query) => {
            if (query !== '') {
                this.loading = true;
                this.$refs.results_modal.open();
                nextTick(() => {
                    this.$refs.search_input.focus();
                });
                this.search(query);
            }
        });
    }
};
</script>