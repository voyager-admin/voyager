<template>
    <card :title="__('voyager::bread.browse_type', { type: translate(bread.name_plural, true) })" :icon="bread.icon">
        <div slot="actions">
            <div class="flex flex-wrap items-center">
                <input
                    type="text"
                    class="input small ltr:mr-2 rtl:ml-2"
                    v-model="parameters.global"
                    @dblclick="parameters.global = null"
                    @keydown.esc="parameters.global = null"
                    :placeholder="__('voyager::bread.search_type', {type: translate(bread.name_plural, true)})">
                <select class="input small ltr:mr-2 rtl:ml-2" v-model="parameters.softdeleted" v-if="uses_soft_deletes">
                    <option value="show">{{ __('voyager::bread.soft_delete_show') }}</option>
                    <option value="hide">{{ __('voyager::bread.soft_delete_hide') }}</option>
                    <option value="only">{{ __('voyager::bread.soft_delete_only') }}</option>
                </select>
                <button class="button small" @click.stop="load">
                    <icon icon="refresh" :class="[loading ? 'animate-spin-reverse' : '']"></icon>
                    <span>{{ __('voyager::generic.reload') }}</span>
                </button>
                <bread-actions :actions="actions" bulk @reload="load" :bread="bread" :selected="selectedEntries" />
                <locale-picker :small="false" v-if="$language.localePicker" />
            </div>
        </div>
        <div>
            <div v-if="layout !== null">
                <div class="inline-flex w-full" v-if="layout.options.filters.length > 0">
                    <badge
                        v-for="(filter, i) in layout.options.filters"
                        v-if="displayFilter(filter)"
                        :key="i"
                        :color="filter.color"
                        @click="setFilter(filter)"
                        :icon="isFilterSelected(filter)"
                    >
                        {{ translate(filter.name, true) }}
                    </badge>
                </div>
                <div class="voyager-table" :class="[loading ? 'loading' : '']">
                    <table>
                        <thead>
                            <tr>
                                <th class="w-2">
                                    <input type="checkbox" class="input" @change="selectAll($event.target.checked)" :checked="allSelected" />
                                </th>
                                <th
                                    v-for="(formfield, key) in layout.formfields" :key="'thead-' + key"
                                    :class="formfield.orderable ? 'cursor-pointer' : ''"
                                    @click="formfield.orderable ? orderBy(formfield.column.column) : ''"
                                    v-tooltip="formfield.orderable ? __('voyager::bread.order_by_field_' + (parameters.order == formfield.column.column && parameters.direction == 'asc' ? 'desc' : 'asc'), { field: formfield.column.column }) : false">
                                    <div class="flex h-full items-center">
                                        {{ translate(formfield.title, true) }}
                                        <icon
                                            v-if="formfield.orderable && parameters.order == formfield.column.column"
                                            :icon="parameters.direction == 'asc' ? 'sort-ascending' : 'sort-descending'"
                                            :size="5" class="ltr:ml-2 rtl:mr-2"
                                        ></icon>
                                    </div>
                                </th>
                                <th class="ltr:text-right rtl:text-left">
                                    {{ __('voyager::generic.actions') }}
                                </th>
                            </tr>
                            <tr>
                                <th></th>
                                <th v-for="(formfield, key) in layout.formfields" :key="'thead-search-' + key">
                                    <component
                                        v-if="formfield.searchable"
                                        v-model="parameters.filters[formfield.column.column]"
                                        :is="'formfield-'+kebab_case(formfield.type)+'-browse'"
                                        :options="formfield.options"
                                        show="query">
                                        <input type="text" class="input small w-full"
                                            :placeholder="__('voyager::bread.search_type', {type: translate(formfield.title, true)})"
                                            @dblclick="parameters.filters[formfield.column.column] = ''"
                                            @keydown.esc="parameters.filters[formfield.column.column] = ''"
                                            v-model="parameters.filters[formfield.column.column]">
                                    </component>
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(result, key) in results" :key="'row-' + key">
                                <td>
                                    <input
                                        type="checkbox"
                                        class="input"
                                        v-model="selected"
                                        :value="result.primary_key"
                                    />
                                </td>
                                <td v-for="(formfield, key) in layout.formfields" :key="'row-' + key">
                                    <component
                                        v-if="$store.getFormfieldByType(formfield.type).browseArray"
                                        :is="'formfield-'+kebab_case(formfield.type)+'-browse'"
                                        :options="formfield.options"
                                        :translatable="formfield.translatable"
                                        :value="getData(result, formfield, true)"
                                    >
                                    </component>
                                    <component
                                        v-if="!isArray(result[formfield.column.column])"
                                        :is="'formfield-'+kebab_case(formfield.type)+'-browse'"
                                        :options="formfield.options"
                                        :translatable="formfield.translatable"
                                        :value="getData(result, formfield, false)"
                                    >
                                    </component>
                                    <div v-else>
                                        <component
                                            v-for="(val, i) in getData(result, formfield, true).slice(0, 3)"
                                            :is="'formfield-'+kebab_case(formfield.type)+'-browse'"
                                            :options="formfield.options"
                                            :translatable="formfield.translatable"
                                            :key="'relationship-'+i"
                                            :value="val">
                                        </component>
                                        <span v-if="getData(result, formfield, true).length > 3">
                                            {{ __('voyager::generic.more_results', {num: getData(result, formfield, true).length - 3}) }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <bread-actions :actions="actions" :selected="[result]" @reload="load" :bread="bread" />
                                </td>
                            </tr>
                            <tr v-if="results.length == 0 && !loading">
                                <td :colspan="layout.formfields.length + 2" class="text-center">
                                    <h4>{{ __('voyager::bread.no_results') }}</h4>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex w-full">
                    <div class="hidden lg:block w-1/2 whitespace-no-wrap">
                        {{ resultDescription }}
                        <a href="#" @click.prevent="parameters.filters = {}; parameters.global = null; parameters.filter = null" v-if="showClearFilterButton">
                            {{ __('voyager::bread.clear_all_filters') }}
                        </a>
                    </div>
                    <div class="w-full lg:w1/2 lg:justify-end inline-flex">
                        <select v-model="parameters.perpage" class="input small ltr:mr-2 rtl:ml-2" v-if="filtered >= 10">
                            <option>10</option>
                            <option v-if="filtered >= 25">25</option>
                            <option v-if="filtered >= 50">50</option>
                            <option v-if="filtered >= 100">100</option>
                        </select>
                        <pagination :page-count="pages" v-model.number="parameters.page" v-if="results.length > 0"></pagination>
                    </div>
                </div>
            </div>
        </div>
    </card>
</template>

<script>
export default {
    props: {
        bread: {
            type: Object,
            required: true,
        },
        perPage: {
            type: Number,
            default: 10,
        }
    },
    data: function () {
        return {
            loading: false,
            results: [],
            layout: null,
            total: 0,    // Total unfiltered amount of entries
            filtered: 0, // Amount of filtered entries
            selected: [], // Array of selected primary-keys
            uses_soft_deletes: false, // If the model uses soft-deleting
            actions: [], // The actions which should be displayed
            parameters: {
                page: 1,
                perpage: this.perPage,
                global: null,
                filters: {},
                order: null,
                direction: 'asc',
                softdeleted: 'show', // show, hide, only
                locale: this.$language.locale,
                filter: null, // The current selected filter
            },
        };
    },
    methods: {
        load: function () {
            var vm = this;
            vm.loading = true;
            axios
            .post(vm.route('voyager.'+vm.translate(vm.bread.slug, true)+'.data'), vm.parameters)
            .then(function (response) {
                for (var key in response.data) {
                    if (response.data.hasOwnProperty(key) && vm.hasOwnProperty(key)) {
                        vm[key] = response.data[key];
                    }
                }

                if (vm.parameters.order === null) {
                    vm.parameters.order = vm.layout.options.default_order_column.column;
                }
                if (response.data.execution > 500) {
                    new vm.$notification(vm.__('voyager::bread.execution_time_warning', { time: parseInt(response.data.execution) })).color('yellow').timeout().show();
                }
            })
            .catch(function (response) {
                new vm.$notification(response.response.data.message).color('red').timeout().show();
            })
            .finally(function () {
                vm.loading = false;
            });
        },
        getData: function (result, formfield, asArray = false) {
            var vm = this;
            var results = result[formfield.column.column];
            if (vm.isArray(results)) {
                if (!asArray) {
                    results = results.slice(0, 3);
                }
                return results.map(function (r) {
                    if (formfield.translatable) {
                        return vm.translate((r || ''), !formfield.translatable);
                    }

                    return r;
                });
            }

            return vm.translate((results || ''), !formfield.translatable);
        },
        orderBy: function (column) {
            if (this.parameters.order == column) {
                this.parameters.direction = this.parameters.direction == 'asc' ? 'desc' : 'asc';
            } else {
                this.parameters.order = column;
                this.parameters.direction = 'asc';
            }
        },
        selectAll: function (checked) {
            var vm = this;
            vm.selected = [];
            if (checked) {
                vm.results.forEach(function (result) {
                    vm.selected.push(result.primary_key);
                });
            }
        },
        pushParameterToUrl: function (params) {
            var url = window.location.href.split('?')[0];
            for (var key in params) {
                if (params.hasOwnProperty(key) && params[key] !== null && key !== 'filter') {
                    if (this.isObject(params[key])) {
                        url = this.addParameterToUrl(key, JSON.stringify(params[key]), url);
                    } else {
                        url = this.addParameterToUrl(key, params[key], url);
                    }
                }
            }
            this.pushToUrlHistory(url);
        },
        setFilter: function (filter) {
            if (this.isFilterSelected(filter)) {
                this.parameters.filter = null;
            } else {
                this.parameters.filter = filter;
            }
        },
        isFilterSelected: function (filter) {
            var p_filter = this.parameters.filter;
            if (filter && p_filter && p_filter.column == filter.column && p_filter.operator == filter.operator && p_filter.value == filter.value) {
                return 'x';
            }

            return '';
        },
        displayFilter: function (filter) {
            return !(filter.column == '' || filter.operator == '' || this.translate(filter.name, true) == '');
        }
    },
    computed: {
        pages: function () {
            return Math.ceil(this.filtered / this.parameters.perpage);
        },
        showClearFilterButton: function () {
            if (this.parameters.global !== null && this.parameters.global !== '') {
                return true;
            }
            return Object.values(this.parameters.filters).whereNot('').length > 0;
        },
        resultDescription: function () {
            var type = this.translate(this.bread.name_plural, true);
            if (this.filtered == 1) {
                type = this.translate(this.bread.name_singular, true);
            }
            var start = 1 + ((this.parameters.page - 1) * this.parameters.perpage);
            if (this.results.length == 0) {
                start = 0;
            }
            var end = this.clamp((start + this.parameters.perpage - 1), start, this.filtered);
            var desc = this.__('voyager::bread.results_description', {
                start: start,
                end  : end,
                total: this.filtered,
                type : type,
            });

            if (this.filtered != this.total) {
                var type = this.translate(this.bread.name_plural, true);
                if (this.total == 1) {
                    type = this.translate(this.bread.name_singular, true);
                }
                desc = desc + ' ' + this.__('voyager::bread.filter_description', {
                    total: this.total,
                    type : type,
                });
            }

            return desc;
        },
        allSelected: function () {
            var vm = this;

            var not_found = false;
            vm.results.forEach(function (result) {
                if (!vm.selected.includes(result.primary_key)) {
                    not_found = true;
                }
            });

            return !not_found;
        },
        selectedEntries: function () {
            var vm = this;
            return vm.results.filter(function (result) {
                return vm.selected.includes(result.primary_key);
            });
        },
    },
    mounted: function () {
        Vue.prototype.$language.localePicker = true;
        var vm = this;

        var parameter_found = false;
        for (var param of vm.getParametersFromUrl()) {
            try {
                var val = JSON.parse(param[1]);
                Vue.set(this.parameters, param[0], val);
            } catch {
                Vue.set(this.parameters, param[0], param[1]);
            }

            parameter_found = true;
        }

        if (!parameter_found) {
            this.pushParameterToUrl(this.parameters);
            this.load();
        }
    },
    watch: {
        selected: function (selected) {
            this.$emit('select', selected);
        },
        'parameters.page': function () {
            this.selected = [];
        },
        parameters: {
            handler: debounce(function (val) {
                this.pushParameterToUrl(val);
                this.load();
            }, 250),
            deep: true,
        },
        'parameters.softdeleted': function () {
            this.parameters.page = 1;
        },
        '$language.locale': function (locale) {
            this.parameters.locale = locale;
        },
    }
};
</script>