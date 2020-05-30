<template>
    <card :title="__('voyager::bread.browse_type', { type: translate(bread.name_plural, true) })" :icon="bread.icon" :show-header="!fromRelationship">
        <div slot="actions">
            <div class="flex items-center">
                <input
                    type="text"
                    class="input w-full small ltr:mr-2 rtl:ml-2"
                    v-model="parameters.global"
                    @dblclick="parameters.global = null"
                    :placeholder="'Search ' + translate(bread.name_plural, true)">
                <select class="input small ltr:mr-2 rtl:ml-2" v-model="parameters.softdeleted" v-if="uses_soft_deletes">
                    <option value="show">{{ __('voyager::bread.soft_delete_show') }}</option>
                    <option value="hide">{{ __('voyager::bread.soft_delete_hide') }}</option>
                    <option value="only">{{ __('voyager::bread.soft_delete_only') }}</option>
                </select>
                <button class="button accent m-0 ml-2" @click.stop="load">
                    <icon icon="refresh" :class="[loading ? 'rotating-ccw' : '']"></icon>
                    <span>{{ __('voyager::generic.reload') }}</span>
                </button>
                <button class="button red m-0 ml-2" v-if="deletableEntries > 0" @click.prevent="deleteEntries(selected)">
                    <icon icon="trash"></icon>
                    <span>{{ trans_choice('voyager::bread.delete_type', deletableEntries, { types: translate(bread.name_plural, true), type: translate(bread.name_singular, true)}) }}</span>
                </button>
                <button class="button red m-0 ml-2" v-if="restorableEntries > 0" @click.prevent="deleteEntries(selected, true)">
                    <icon icon="trash"></icon>
                    <span>{{ trans_choice('voyager::bread.force_delete_type', restorableEntries, { types: translate(bread.name_plural, true), type: translate(bread.name_singular, true) }) }}</span>
                </button>
                <button class="button green m-0 ml-2" v-if="restorableEntries > 0" @click.prevent="restoreEntries(selected)">
                    <icon icon="history"></icon>
                    <span>{{ trans_choice('voyager::bread.restore_type', restorableEntries, { types: translate(bread.name_plural, true), type: translate(bread.name_singular, true) }) }}</span>
                </button>
                <a class="button green m-0 ml-2" :href="route('voyager.'+translate(bread.slug, true)+'.add')">
                    <icon icon="plus"></icon>
                    <span>{{ __('voyager::generic.add') }}</span>
                </a>
                <locale-picker class="m-0 ml-2" :small="false" v-if="$language.localePicker" />
            </div>
        </div>
        <div>
            <div v-if="layout !== null">
                <div class="voyager-table" :class="[loading ? 'loading' : '']">
                    <table>
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="input" @change="selectAll($event.target.checked)" :checked="allSelected" v-if="relationshipMultiple" />
                                    <button class="button blue" v-if="fromRelationship && !relationshipMultiple" @click="$emit('select', []); selected = []">
                                        {{ __('voyager::generic.none') }}
                                    </button>
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
                                <th class="ltr:text-right rtl:text-left" v-if="!fromRelationship">
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
                                            :placeholder="'Search ' + translate(formfield.title, true)"
                                            @dblclick="parameters.filters[formfield.column.column] = ''"
                                            v-model="parameters.filters[formfield.column.column]">
                                    </component>
                                </th>
                                <th v-if="!fromRelationship"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(result, key) in results" :key="'row-' + key">
                                <td>
                                    <input
                                        v-if="relationshipMultiple"
                                        type="checkbox"
                                        class="input"
                                        v-model="selected"
                                        :value="result[primary]" />

                                    <input
                                        v-else
                                        type="radio"
                                        class="input"
                                        :name="'radio-'+_uid"
                                        :checked="selected.includes(result[primary])"
                                        @change="selected = []; selected.push(result[primary])" />
                                </td>
                                <td v-for="(formfield, key) in layout.formfields" :key="'row-' + key">
                                    <component
                                        v-if="!isArray(result[formfield.column.column]) || $store.getFormfieldByType(formfield.type).browseArray"
                                        :is="'formfield-'+kebab_case(formfield.type)+'-browse'"
                                        :options="formfield.options"
                                        :translatable="formfield.translatable"
                                        :value="getData(result, formfield, $store.getFormfieldByType(formfield.type).browseArray)">
                                    </component>
                                    <div v-else>
                                        <component
                                            v-for="(val, i) in getData(result, formfield, true)"
                                            :is="'formfield-'+kebab_case(formfield.type)+'-browse'"
                                            :options="formfield.options"
                                            :translatable="formfield.translatable"
                                            :key="'relationship-'+i"
                                            :value="val">
                                        </component>
                                    </div>
                                </td>
                                <td class="flex justify-end" v-if="!fromRelationship">
                                    <div class="button-group flex-no-wrap">
                                        <a :href="route('voyager.'+translate(bread.slug, true)+'.read', result[primary])" class="button blue small">
                                            <icon icon="book-open"></icon>
                                            <span>{{ __('voyager::generic.read') }}</span>
                                        </a>
                                        <a :href="route('voyager.'+translate(bread.slug, true)+'.edit', result[primary])" class="button yellow small">
                                            <icon icon="pencil"></icon>
                                            <span>{{ __('voyager::generic.edit') }}</span>
                                        </a>
                                        <button @click.prevent="deleteEntries(result[primary])" class="button red small" v-if="(uses_soft_deletes && !result.is_soft_deleted) || !uses_soft_deletes">
                                            <icon icon="trash"></icon>
                                            <span>{{ __('voyager::generic.delete') }}</span>
                                        </button>
                                        <button @click.prevent="deleteEntries(result[primary], true)" v-if="uses_soft_deletes && result.is_soft_deleted" class="button red small">
                                            <icon icon="trash"></icon>
                                            <span>{{ __('voyager::generic.force_delete') }}</span>
                                        </button>
                                        <button @click.prevent="restoreEntries(result[primary])" v-if="uses_soft_deletes && result.is_soft_deleted" class="button green small">
                                            <icon icon="history"></icon>
                                            <span>{{ __('voyager::generic.restore') }}</span>
                                        </button>
                                    </div>
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
                        <a href="#" @click.prevent="parameters.filters = {}; parameters.global = null" v-if="showClearFilterButton">
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
        fromRelationship: {
            type: Boolean,
            default: false,
        },
        relationshipLayout: {
            type: Object,
            default: null,
        },
        relationshipSelected: {
            type: Array,
            default: function () {
                return [];
            }
        },
        relationshipMultiple: {
            type: Boolean,
            default: true
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
            total: 0,    // Total unfiltered amount of entries
            filtered: 0, // Amount of filtered entries
            layout: this.relationshipLayout,
            selected: this.relationshipSelected, // Array of selected primary-keys
            primary: 'id', // The primary key
            uses_soft_deletes: false, // If the model uses soft-deleting
            translatable: false, // If the layout contains translatable fields (will show/hide the locale picker)
            parameters: {
                page: 1,
                perpage: this.perPage,
                global: null,
                filters: {},
                order: null,
                direction: 'asc',
                softdeleted: 'show', // show, hide, only
                locale: this.$language.locale,
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
                    if (key == 'layout' && vm.relationshipLayout) {
                        // Do nothing
                    } else if (response.data.hasOwnProperty(key) && vm.hasOwnProperty(key)) {
                        vm[key] = response.data[key];
                    }
                }

                if (vm.parameters.order === null) {
                    vm.parameters.order = vm.layout.options.default_order_column.column;
                }
                if (response.data.execution > 500) {
                    vm.$notify.notify(vm.__('voyager::bread.execution_time_warning', { time: parseInt(response.data.execution) }), null, 'yellow', 7500);
                }
            })
            .catch(function (response) {
                vm.$notify.notify(response.response.data.message, null, 'red', 7500);
            })
            .finally(function () {
                vm.loading = false;
            });
        },
        getData: function (result, formfield, asArray = false) {
            var vm = this;
            if (asArray && vm.isArray(result[formfield.column.column])) {
                return result[formfield.column.column].slice(0, 3).map(function (r) {
                    return vm.translate((r || ''), !formfield.translatable);
                });
            }

            return vm.translate((result[formfield.column.column] || ''), !formfield.translatable);
        },
        orderBy: function (column) {
            if (this.parameters.order == column) {
                if (this.parameters.direction == 'asc') {
                    this.parameters.direction = 'desc';
                } else {
                    this.parameters.direction = 'asc';
                }
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
                    vm.selected.push(result[vm.primary]);
                });
            }
        },
        deleteEntries: function (entries, force = false) {
            var vm = this;
            vm.$notify.confirm(
                vm.trans_choice(
                    'voyager::bread.' + (force ? 'force_delete_type_confirm' : 'delete_type_confirm'),
                    (force ? vm.restorableEntries : vm.deletableEntries),
                    {
                        num: (force ? vm.restorableEntries : vm.deletableEntries),
                        types: vm.translate(vm.bread.name_plural, true),
                        type: vm.translate(vm.bread.name_singular, true)
                    }
                ),
                function (response) {
                    if (response) {
                        axios.delete(vm.route('voyager.'+vm.translate(vm.bread.slug, true)+'.delete'), {
                            params: {
                                ids: entries,
                                force: force
                            }
                        })
                        .then(function (response) {
                            vm.$notify.notify(
                                vm.trans_choice('voyager::bread.' + (force ? 'force_delete_type_success' : 'delete_type_success'),
                                response.data,
                                {
                                    num: response.data,
                                    type: vm.translate(vm.bread.name_singular, true),
                                    types: vm.translate(vm.bread.name_plural, true)
                                }),
                                null,
                                'green',
                                5000
                            );
                        })
                        .catch(function (errors) {
                            //
                        })
                        .then(function () {
                            vm.load();
                        });
                    }
                },
                false,
                'red',
                vm.__('voyager::generic.yes'),
                vm.__('voyager::generic.no'),
                7500
            );
        },
        restoreEntries: function (entries) {
            var vm = this;
            vm.$notify.confirm(
                vm.trans_choice(
                    'voyager::bread.restore_type_confirm',
                    (vm.isArray(entries) ? entries.length : 1),
                    {
                        num: vm.restorableEntries,
                        types: vm.translate(vm.bread.name_plural, true),
                        type: vm.translate(vm.bread.name_singular, true)
                    }
                ),
                function (response) {
                    if (response) {
                        axios.patch(vm.route('voyager.'+vm.translate(vm.bread.slug, true)+'.restore'), {
                            ids: entries
                        })
                        .then(function (response) {
                            vm.$notify.notify(
                                vm.trans_choice('voyager::bread.restore_type_success',
                                response.data,
                                {
                                    num: response.data,
                                    type: vm.translate(vm.bread.name_singular, true),
                                    types: vm.translate(vm.bread.name_plural, true)
                                }),
                                null,
                                'green',
                                5000
                            );
                        })
                        .catch(function (errors) {
                            //
                        })
                        .then(function () {
                            vm.load();
                        });
                    }
                },
                false,
                'green',
                vm.__('voyager::generic.yes'),
                vm.__('voyager::generic.no'),
                7500
            );
        },
    },
    computed: {
        pages: function () {
            return Math.ceil(this.filtered / this.parameters.perpage);
        },
        showClearFilterButton: function () {
            if (this.parameters.global !== null && this.parameters.global !== '') {
                return true;
            }
            return Object.values(this.parameters.filters).filter(function (filter) {
                return filter !== '';
            }).length > 0;
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
                if (!vm.selected.includes(result[vm.primary])) {
                    not_found = true;
                }
            });

            return !not_found;
        },
        // Returns the number of entries which are selected and are NOT soft-deleted
        deletableEntries: function () {
            var vm = this;
            return vm.results.filter(function (result) {
                return !result.is_soft_deleted && vm.selected.includes(result[vm.primary]);
            }).length;
        },
        // Returns the number of entries which are selected and ARE soft-deleted (can also be used for force-delete)
        restorableEntries: function () {
            var vm = this;
            return vm.results.filter(function (result) {
                return result.is_soft_deleted && vm.selected.includes(result[vm.primary]);
            }).length;
        },
    },
    mounted: function () {
        var parameter_found = false;
        for (var param of this.getParametersFromUrl()) {
            try {
                var val = JSON.parse(param[1]);
                Vue.set(this.parameters, param[0], val);
            } catch {
                Vue.set(this.parameters, param[0], param[1]);
            }

            parameter_found = true;
        }

        // Data will automatically be loaded in the watcher when parameters were set above
        if (!parameter_found) {
            this.load();
        }
    },
    watch: {
        selected: function (selected, old) {
            this.$emit('select', selected);
        },
        'parameters.page': function () {
            this.selected = [];
        },
        parameters: {
            handler: debounce(function (val) {
                // Remove all parameters from URL
                if (!this.fromRelationship) {
                    var url = window.location.href.split('?')[0];
                    for (var key in val) {
                        if (val.hasOwnProperty(key) && val[key] !== null) {
                            if (this.isObject(val[key])) {
                                url = this.addParameterToUrl(key, JSON.stringify(val[key]), url);
                            } else {
                                url = this.addParameterToUrl(key, val[key], url);
                            }
                        }
                    }
                    this.pushToUrlHistory(url);
                }
                this.load();
            }, 250),
            deep: true,
        },
        '$language.locale': function (locale) {
            this.parameters.locale = locale;
        },
        translatable: function (value) {
            Vue.prototype.$language.localePicker = value;
        }
    }
};
</script>