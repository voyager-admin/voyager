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
                <draggable as="tbody" :modelValue="formfields" @update:modelValue="$emit('update:formfields', $event)" handle=".dd-handle">
                    <tr v-for="formfield in formfields" :key="formfield.uuid" class="dd-source">
                        <td class="hidden md:table-cell dd-handle cursor-move" v-tooltip="__('voyager::builder.move')">
                            <icon icon="selector" />
                        </td>
                        <td class="hidden md:table-cell">{{ getFormfieldByType(formfield.type).name }}</td>
                        <td>
                            <select class="input small w-full" v-model="formfield.column">
                                <optgroup :label="__('voyager::builder.columns')" v-if="getFormfieldByType(formfield.type).allow_columns">
                                    <option v-for="(column, i) in columns" :key="'column_'+i" :value="{column: column, type: 'column'}">
                                        {{ column }}
                                    </option>
                                </optgroup>
                                <optgroup :label="__('voyager::builder.computed')" v-if="getFormfieldByType(formfield.type).allow_computed_props">
                                    <option v-for="(prop, i) in computed" :key="'computed_'+i" :value="{column: prop, type: 'computed'}">
                                        {{ prop }}
                                    </option>
                                </optgroup>
                                <template v-for="(relationship, i) in relationships" :key="'relationship_'+i">
                                    <optgroup :label="relationship.method" v-if="getFormfieldByType(formfield.type).allow_relationship_props">
                                        <option v-for="(column, i) in relationship.columns" :key="'column_'+i" :value="{column: relationship.method+'.'+column, type: 'relationship'}">
                                            {{ column }}
                                        </option>
                                        <template v-for="(column, i) in relationship.pivot" :key="'pivot_'+i">
                                            <option :value="{column: relationship.method+'.pivot.'+column, type: 'relationship'}" v-if="getFormfieldByType(formfield.type).allow_relationship_pivots">
                                                pivot.{{ column }}
                                            </option>
                                        </template>
                                    </optgroup>
                                </template>
                                <optgroup v-if="getFormfieldByType(formfield.type).allow_relationships" :label="__('voyager::generic.relationships')">
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
                                v-model="formfield.title" />
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
                                v-model="options.default_order_column"
                                v-bind:value="formfield.column" />
                        </td>
                        <td class="hidden md:table-cell">
                            <input
                                type="checkbox"
                                class="input"
                                v-model="formfield.translatable"
                                :disabled="!getFormfieldByType(formfield.type).can_be_translated">
                        </td>
                        <td class="flex flex-no-wrap justify-end">
                            <slide-in :title="__('voyager::generic.options')">
                                <template #actions>
                                    <locale-picker />
                                </template>
                                <component
                                    :is="getFormfieldByType(formfield.type).builder_component"
                                    v-model:options="formfield.options"
                                    :column="formfield.column"
                                    :columns="columns"
                                    action="list-options" />

                                <template #opener>
                                    <button class="button">
                                        <icon icon="cog" />
                                        <span>{{ __('voyager::generic.options') }}</span>
                                    </button>
                                </template>
                            </slide-in>
                            <button class="button red" @click="$emit('delete', key)">
                                <icon icon="trash" />
                                <span>{{ __('voyager::generic.delete') }}</span>
                            </button>
                        </td>
                    </tr>
                </draggable>
            </table>
        </div>

        <collapsible :title="`${__('voyager::generic.filters')} (${options.filters.length || 0})`" closed ref="filters_collapsible">
            <template #actions>
                <button class="button green small" @click.stop="addFilter">
                    <icon icon="plus" />
                </button>
            </template>
            <div class="voyager-table">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('voyager::generic.name') }}</th>
                            <th>{{ __('voyager::generic.column') }}</th>
                            <th>{{ __('voyager::generic.operator') }}</th>
                            <th>{{ __('voyager::builder.value_or_scope') }}</th>
                            <th>{{ __('voyager::generic.color') }}</th>
                            <th>{{ __('voyager::generic.icon') }}</th>
                            <th>{{ __('voyager::generic.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(f, key) in options.filters" :key="'filter-'+key">
                            <td>
                                <language-input
                                    class="input small w-full"
                                    type="text" :placeholder="__('voyager::generic.name')"
                                    v-model="f.name"
                                />
                            </td>
                            <td>
                                <select class="input small w-full" v-model="f.column">
                                    <option :value="null">{{ __('voyager::generic.none') }}</option>
                                    <option v-for="column in columns" :key="column">{{ column }}</option>
                                </select>
                            </td>
                            <td>
                                <select class="input small w-full" v-model="f.operator" :disabled="f.column === null">
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
                                <color-picker v-model="f.color" :size="2" add-none />
                            </td>
                            <td>
                                <modal :ref="`filter_icon_modal_${key}`" :title="__('voyager::generic.select_icon')">
                                    <icon-picker v-on:select="$refs['filter_icon_modal_'+key].close(); f.icon = $event" />
                                    <template #opener>
                                        <div class="w-full">
                                            <button class="button">
                                                <icon class="my-1 content-center" :icon="f.icon ? f.icon : 'ban'" :key="key + (f.icon ? f.icon : 'ban')" />
                                            </button>
                                        </div>
                                    </template>
                                    <template #actions>
                                        <button class="button" @click="f.icon = null; $refs['filter_icon_modal_'+key].close()">
                                            {{ __('voyager::generic.none') }}
                                        </button>
                                    </template>
                                </modal>
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
import Draggable from '../UI/Draggable';

export default {
    components: { Draggable },
    emits: ['update:formfields', 'update:options', 'delete'],
    props: ['computed', 'columns', 'relationships', 'formfields', 'options'],
    data() {
        return {
            colors: this.colors,
        };
    },
    methods: {
        addFilter() {
            this.$refs.filters_collapsible.open();
            var options = this.options;
            if (!this.isArray(options.filters)) {
                options.filters = [];
            }
            options.filters.push({
                name: '',
                column: null,
                operator: '=',
                value: '',
                color: 'accent',
                icon: null,
            });
            this.$emit('update:options', options);
        },
        removeFilter(key) {
            var options = this.options;
            options.filters.splice(key, 1);
            this.$emit('update:options', options);
        }
    }
};
</script>