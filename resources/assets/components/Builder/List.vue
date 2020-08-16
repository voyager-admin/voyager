<template>
    <div>
        <div class="voyager-table striped mt-0">
            <table>
                <thead>
                    <tr>
                        <th class="hidden md:table-cell"></th>
                        <th class="hidden md:table-cell">{{ __('voyager::generic.type') }}</th>
                        <th>{{ __('voyager::generic.column') }}</th>
                        <th>{{ __('voyager::generic.title') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::builder.searchable') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::builder.orderable') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::builder.order_default') }}</th>
                        <th class="hidden md:table-cell">{{ __('voyager::generic.translatable') }}</th>
                        <th style="text-align:right !important">{{ __('voyager::generic.actions') }}</th>
                    </tr>
                </thead>
                <sort-container v-model="reactiveFormfields" tag="tbody" :useDragHandle="true">
                    <sort-element tag="tr" v-for="(formfield, key) in reactiveFormfields" :key="'formfield-'+key" :index="key">
                        <td class="hidden md:table-cell">
                            <icon icon="selector" v-sort-handle class="cursor-move" :size="5" />
                        </td>
                        <td class="hidden md:table-cell">{{ $store.getFormfieldByType(formfield.type).name }}</td>
                        <td>
                            <select class="input small w-full" v-model="formfield.column">
                                <optgroup :label="__('voyager::builder.columns')" v-if="$store.getFormfieldByType(formfield.type).allowColumns">
                                    <option v-for="(column, i) in columns" :key="'column_'+i" :value="{column: column, type: 'column'}">
                                        {{ column }}
                                    </option>
                                </optgroup>
                                <optgroup :label="__('voyager::builder.computed')" v-if="$store.getFormfieldByType(formfield.type).allowComputed">
                                    <option v-for="(prop, i) in computed" :key="'computed_'+i" :value="{column: prop, type: 'computed'}">
                                        {{ prop }}
                                    </option>
                                </optgroup>
                                <optgroup v-for="(relationship, i) in relationships" :key="'relationship_'+i" :label="relationship.method" v-if="$store.getFormfieldByType(formfield.type).allowRelationshipColumns">
                                    <option v-for="(column, i) in relationship.columns" :key="'column_'+i" :value="{column: relationship.method+'.'+column, type: 'relationship'}">
                                        {{ column }}
                                    </option>
                                    <option v-for="(column, i) in relationship.pivot" :key="'pivot_'+i" :value="{column: relationship.method+'.pivot.'+column, type: 'relationship'}" v-if="$store.getFormfieldByType(formfield.type).allowPivot">
                                        pivot.{{ column }}
                                    </option>
                                    <option :value="{column: relationship.method+'.relationship_amount', type: 'relationship'}">{{ __('voyager::generic.relationship_amount') }}</option>
                                </optgroup>
                                <optgroup v-if="$store.getFormfieldByType(formfield.type).allowRelationships" :label="__('voyager::generic.relationships')">
                                    <option v-for="(relationship, i) in relationships" :key="'relationship_'+i" :value="{column: relationship.method, type: 'relationship'}">
                                        {{ relationship.method }}
                                    </option>
                                </optgroup>
                            </select>
                        </td>
                        <td>
                            <language-input
                                class="input small w-full"
                                type="text" placeholder="Title"
                                v-bind:value="formfield.title"
                                v-on:input="formfield.title = $event" />
                        </td>
                        <td class="hidden md:table-cell">
                            <input
                                class="input"
                                type="checkbox"
                                v-model="formfield.searchable"
                                :disabled="(formfield.column.type !== 'column' && formfield.column.type !== 'relationship') || (formfield.column.type == 'relationship' && formfield.column.column.includes('relationship_amount'))" />
                        </td>
                        <td class="hidden md:table-cell">
                            <input
                                class="input"
                                type="checkbox"
                                v-model="formfield.orderable"
                                :disabled="formfield.column.type !== 'column'" />
                        </td>
                        <td class="hidden md:table-cell">
                            <input
                                class="input"
                                type="radio"
                                :disabled="formfield.column.type !== 'column'"
                                v-model="reactiveOptions.default_order_column"
                                v-bind:value="formfield.column" />
                        </td>
                        <td class="hidden md:table-cell">
                            <input
                                type="checkbox"
                                class="input"
                                v-model="formfield.translatable"
                                :disabled="!$store.getFormfieldByType(formfield.type).canBeTranslated">
                        </td>
                        <td class="inline-flex">
                            <button class="button" @click="$emit('open-options', key)">
                                <icon icon="cog" />
                                <span>{{ __('voyager::generic.options') }}</span>
                            </button>
                            <button class="button red" @click="$emit('delete', key)">
                                <icon icon="trash" />
                                <span>{{ __('voyager::generic.delete') }}</span>
                            </button>
                            <slide-in :opened="optionsId == key" width="w-1/3" v-on:closed="$emit('open-options', null)" class="text-left" :title="__('voyager::generic.options')">
                                <locale-picker v-if="$language.localePicker" slot="actions" />
                                <component
                                    :is="'formfield-'+kebab_case(formfield.type)+'-builder'"
                                    v-bind:options="formfield.options"
                                    :column="formfield.column"
                                    show="list-options" />
                            </slide-in>
                        </td>
                    </sort-element>
                </sort-container>
            </table>
        </div>

        <collapsible :title="__('voyager::generic.filters')" closed>
            <div slot="actions">
                <button class="button green small" @click.stop="addFilter">
                    <icon icon="plus" />
                </button>
            </div>
            <div class="voyager-table">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('voyager::generic.name') }}</th>
                            <th>{{ __('voyager::generic.column') }}</th>
                            <th>{{ __('voyager::generic.operator') }}</th>
                            <th>{{ __('voyager::generic.value') }}</th>
                            <th>{{ __('voyager::generic.color') }}</th>
                            <th>{{ __('voyager::generic.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(f, key) in reactiveOptions.filters" :key="'filter-'+key">
                            <td>
                                <language-input
                                    class="input w-full"
                                    type="text" :placeholder="__('voyager::generic.name')"
                                    v-bind:value="f.name"
                                    v-on:input="f.name = $event" />
                            </td>
                            <td>
                                <select class="input small w-full" v-model="f.column">
                                    <option v-for="column in columns" :key="column">{{ column }}</option>
                                </select>
                            </td>
                            <td>
                                <select class="input small w-full" v-model="f.operator">
                                    <option value="=">{{ __('voyager::builder.operators.equals') }}</option>
                                    <option value="!=">{{ __('voyager::builder.operators.not_equals') }}</option>
                                    <option value=">=">{{ __('voyager::builder.operators.bigger_than') }}</option>
                                    <option value=">">{{ __('voyager::builder.operators.bigger') }}</option>
                                    <option value="<=">{{ __('voyager::builder.operators.smaller_than') }}</option>
                                    <option value="<">{{ __('voyager::builder.operators.smaller') }}</option>
                                    <option value="like">{{ __('voyager::builder.operators.like') }}</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="input small w-full" v-model="f.value">
                            </td>
                            <td>
                                <select class="input small w-full" v-model="f.color">
                                    <option
                                        v-for="(color, i) in $store.ui.colors"
                                        :value="color"
                                        :key="i"
                                    >
                                        {{ __('voyager::generic.color_names.'+color) }}
                                    </option>
                                </select>
                            </td>
                            <td>
                                <button class="button red small" @click.stop="removeFilter(key)">
                                    <icon icon="trash" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </collapsible>
    </div>
</template>

<script>
export default {
    props: ['computed', 'columns', 'relationships', 'formfields', 'optionsId', 'options'],
    data: function () {
        return {
            reactiveFormfields: this.formfields,
            reactiveOptions: this.options,
        };
    },
    methods: {
        addFilter: function () {
            if (!this.isArray(this.reactiveOptions.filters)) {
                Vue.set(this.reactiveOptions, 'filters', []);
            }
            this.reactiveOptions.filters.push({
                name: '',
                column: '',
                operator: '=',
                value: '',
                color: 'accent',
            });
        },
        removeFilter: function (key) {
            this.reactiveOptions.filters.splice(key, 1);
        }
    },
    watch: {
        reactiveFormfields: function (formfields) {
            this.$emit('formfields', formfields);
        },
        reactiveOptions: function (options) {
            this.$emit('options', options);
        },
        formfields: function (formfields) {
            this.reactiveFormfields = formfields;
        },
        options: function (options) {
            this.reactiveOptions = options;
        }
    },
};
</script>