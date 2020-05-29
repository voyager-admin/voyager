<template>
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
                    <td class="hidden md:table-cell w-5 h-5">
                        <icon icon="selector" v-sort-handle class="cursor-move" />
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
                            :disabled="formfield.column.type !== 'column'" />
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
                    <td class="text-right">
                        <button class="button blue" @click="$emit('open-options', key)">
                            <icon icon="cog" />
                            <span>{{ __('voyager::generic.options') }}</span>
                        </button>
                        <button class="button red" @click="$emit('delete', key)">
                            <icon icon="trash" />
                            <span>{{ __('voyager::generic.delete') }}</span>
                        </button>
                        <slide-in :opened="optionsId == key" width="w-full md:w-1/3" v-on:closed="$emit('open-options', null)" class="text-left" :title="__('voyager::generic.options')">
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