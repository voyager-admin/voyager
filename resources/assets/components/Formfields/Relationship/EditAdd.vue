<template>
    <div>
        <div v-if="addView && options.editable && fromRelationship !== true">
            <modal
                :title="__('voyager::generic.add_type', { type: translate(relatedBread.name_singular, true) })"
                :icon="relatedBread.icon"
                ref="add_modal"
            >
                <bread-edit-add
                    :bread="add_data.bread"
                    action="add"
                    :input="add_data.data"
                    :layout="addView"
                    :relationships="add_data.relationships"
                    :new="true"
                    :prevUrl="''"
                    :fromRelationship="true"
                    @saved="added($event)"
                />
                <template v-slot:actions>
                    <locale-picker :small="false" class="ltr:mr-2 rtl:ml-2"></locale-picker>
                </template>
            </modal>
        </div>
        <!-- Selected -->
        <div class="w-full flex" v-if="options.editable">
            <div class="flex-grow">
                <div was="fade-transition" :group="relationship.multiple" :duration="500">
                    <template v-for="(option, i) in value" :key="i">
                        <badge
                            :icon="options.editable && relationship.multiple ? 'x' : ''"
                            @click-icon.stop.prevent="remove(option)"
                            v-if="option.key !== null"
                        >
                            {{ translate(option.value, false, '&nbsp;') }}
                        </badge>
                    </template>
                </div>
            </div>
            <div class="flex-none" v-if="addView && options.editable && fromRelationship !== true">
                <button class="button green" @click="fetchRelationshipData">
                    <icon icon="refresh" class="animate-spin-reverse" v-if="fetching_add_data" />
                    <icon icon="plus" v-else />
                    {{ __('voyager::generic.add_type', { type: translate(relatedBread.name_singular, true) }) }}
                </button>
            </div>
            
        </div>
        <!-- Selectable -->
        <div>
            <div class="voyager-table">
                <table>
                    <thead>
                        <tr>
                            <th class="w-2" v-if="options.editable">
                                <input
                                    type="checkbox"
                                    class="input"
                                    @change="selectAll($event.target.checked)"
                                    :checked="allSelected"
                                    :disabled="!relationship.multiple || !options.editable"
                                />
                            </th>
                            <th>
                                <input class="input small w-full my-2" v-model="query" :placeholder="translate(options.search_text, true)" />
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td :colspan="relationship.multiple ? 2 : 1">
                                {{ __('voyager::generic.loading_please_wait') }}
                            </td>
                        </tr>
                        <tr v-for="(option, i) in selectable" :key="i" @click="select(option)" class="cursor-pointer">
                            <td v-if="options.editable">
                                <input
                                    :type="relationship.multiple ? 'checkbox' : 'radio'"
                                    class="input"
                                    :checked="selected(option)"
                                    :disabled="!options.editable"
                                />
                            </td>
                            <td>
                                {{ option.key === null ? __('voyager::generic.none') : translate(option.value) }}
                            </td>
                        </tr>
                        <tr v-if="!loading && (options.allow_null ? selectable.length == 1 : selectable.length == 0)">
                            <td :colspan="relationship.multiple ? 2 : 1">
                                {{ __('voyager::generic.no_results') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <pagination :page-count="pages" v-model.number="page" small :first-last-buttons="false" />
        </div>
    </div>
</template>

<script>
import fetch from '../../../js/fetch';

export default {
    props: ['options', 'value', 'column', 'relationships', 'bread', 'translatable', 'fromRelationship', 'primaryKey'],
    data: function () {
        return {
            loading: false,
            initial_loaded: false,
            page: 1,
            pages: 1,
            query: null,
            selectable: [],
            fetching_add_data: false,
            add_data: {}
        };
    },
    computed: {
        relationship: function () {
            return this.relationships.where('method', this.column.column).first() || null;
        },
        relatedBread: function () {
            if (this.relationship) {
                return this.$store.getBreadByTable(this.relationship.table);
            }

            return null;
        },
        addView: function () {
            if (this.options.add_view && this.relationship && this.relatedBread) {
                return this.relatedBread.layouts.where('type', 'view').where('name', this.options.add_view).first();
            }

            return null;
        },
        allSelected: function () {
            var all = true;
            var vm = this;

            vm.selectable.forEach(function (option) {
                if (option.key !== null && vm.value.where('key', option.key).length == 0) {
                    all = false;
                }
            });

            return all;
        }
    },
    methods: {
        loadResults: function () {
            var vm = this;
            vm.loading = true;
            fetch.post(vm.route('voyager.'+vm.translate(this.bread.slug, true)+'.relationship'), {
                query: vm.query,
                method: vm.relationship.method,
                page: vm.page,
                column: vm.options.display_column,
                scope: vm.options.scope,
                editable: vm.options.editable,
                primary: vm.primaryKey,
            })
            .then(function (response) {
                vm.selectable = response.data.data;
                vm.pages = response.data.pages;
                if (vm.options.allow_null && vm.options.editable) {
                    vm.selectable.unshift({
                        key: null,
                        value: vm.__('voyager::generic.none'),
                    });
                }
                vm.initial_loaded = true;
            })
            .catch(function (response) {
                vm.$store.handleAjaxError(response);
            })
            .then(function () {
                vm.loading = false;
            });
        },
        fetchRelationshipData: function () {
            var vm = this;
            vm.fetching_add_data = true;

            fetch.get(vm.route('voyager.'+vm.translate(vm.relatedBread.slug, true)+'.add'), { from_relationship: true })
            .then(function (response) {
                vm.add_data = response.data;
                vm.$refs.add_modal.open();
            })
            .catch(function (response) {
                vm.$store.handleAjaxError(response);
            })
            .then(function () {
                vm.fetching_add_data = false;
            });
        },
        added: function (data) {
            if (!this.options.editable) {
                return;
            }

            var new_entry = {
                key: data.key,
                value: data.data[this.options.display_column]
            }
            this.select(new_entry);
            this.loadResults();
            this.$refs.add_modal.close();
        },
        remove: function (option) {
            if (!this.options.editable) {
                return;
            }
            // TODO: Check if null is allowed
            this.$emit('input', this.value.whereNot('key', option.key));
        },
        select: function (option) {
            if (!this.options.editable) {
                return;
            }

            if (option.key === null && this.options.allow_null) {
                this.$emit('input', []);
                return;
            }

            if (this.relationship.multiple) {
                if (this.selected(option)) {
                    this.remove(option);
                } else {
                    this.$emit('input', [...this.value, option]);
                }
            } else {
                this.$emit('input', [option]);
            }
        },
        selected: function (option) {
            if (option.key === null) {
                return this.value.length == 0;
            }

            return this.value.where('key', option.key).length !== 0;
        },
        selectAll: function (select) {
            if (!this.options.editable) {
                return;
            }

            var vm = this;
            if (vm.relationship.multiple) {
                var value = vm.value;
                if (!select) {
                    // TODO: Check if null is allowed
                    vm.selectable.forEach(function (option) {
                        value = value.whereNot('key', option.key);
                    });
                } else {
                    vm.selectable.forEach(function (option) {
                        if (!vm.selected(option)) {
                            value.push(option);
                        }
                    });
                }

                vm.$emit('input', value);
            }
        }
    },
    watch: {
        page: function () {
            this.loadResults();
        },
        query: debounce(function (query) {
            this.loadResults();
        }, 250)
    },
    mounted: function () {
        this.query = '';
    }
};
</script>