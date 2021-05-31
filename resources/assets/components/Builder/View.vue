<template>
    <div>
        <draggable class="flex flex-wrap w-full min-h-64" :modelValue="formfields" @update:modelValue="$emit('update:formfields', $event)" item-key="" group="view-builder-dd" handle=".dd-handle">
            <template #item="{ element: formfield, index: key }">
                <div
                    class="m-0" :class="formfield.options.width"
                    uses="w-1/6 w-2/6 w-3/6 w-4/6 w-5/6 w-full"
                >
                    <card :title="translate(formfield.options.title) || ''" :title-size="5">
                        <template #actions>
                            <div class="flex space-x-1">
                                <button class="button small dd-handle cursor-move" v-tooltip="__('voyager::generic.move')">
                                    <icon icon="arrows-expand" />
                                </button>
                                <button class="button small" @mousedown="startResize(key)" v-tooltip="__('voyager::builder.resize')">
                                    <icon icon="switch-horizontal" class="cursor-move" />
                                </button>
                                <slide-in :title="__('voyager::generic.options')">
                                    <template #actions>
                                        <locale-picker />
                                    </template>
                                    <label class="label mt-4">{{ __('voyager::generic.column') }}</label>
                                    <!-- TODO: Hide this if formfield doesn't allow any kind of column -->
                                    <select class="input w-full" v-model="formfield.column" v-if="!fromRepeater">
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
                                        <optgroup v-if="getFormfieldByType(formfield.type).allow_relationship_props" :label="__('voyager::generic.relationships')">
                                            <option v-for="(relationship, i) in relationships" :key="'relationship_'+i" :value="{column: relationship.method, type: 'relationship'}">
                                                {{ relationship.method }}
                                            </option>
                                        </optgroup>
                                    </select>
                                    <div v-else>
                                        <label class="label mt-4">{{ __('voyager::generic.key') }}</label>
                                        <input
                                            class="input w-full"
                                            type="text" :placeholder="__('voyager::generic.key')"
                                            v-model="formfield.column.column" />
                                    </div>
                                    <div v-if="getFormfieldByType(formfield.type).can_be_translated">
                                        <label class="label mt-4">{{ __('voyager::generic.translatable') }}</label>
                                        <input type="checkbox" class="input" v-model="formfield.translatable">
                                    </div>

                                    <label class="label mt-4">{{ __('voyager::generic.title') }}</label>
                                    <language-input
                                        class="input w-full"
                                        type="text" :placeholder="__('voyager::generic.title')"
                                        v-model="formfield.options.title" />

                                    <label class="label mt-4">{{ __('voyager::generic.description') }}</label>
                                    <language-input
                                        class="input w-full"
                                        type="text" :placeholder="__('voyager::generic.description')"
                                        v-model="formfield.options.description" />

                                    <component
                                        :is="getFormfieldByType(formfield.type).builder_component"
                                        v-model:options="formfield.options"
                                        :column="formfield.column"
                                        :columns="columns"
                                        v-bind:relationships="relationships"
                                        action="view-options" />
                                    <breadBuilderValidation v-model="formfield.validation" />

                                    <div>
                                        <label class="label mt-4">{{ __('voyager::generic.classes') }}</label>
                                        <input type="text" class="input w-full" v-model="formfield.options.classes">
                                    </div>

                                    <template #opener>
                                        <button class="button small">
                                            <icon icon="cog" />
                                        </button>
                                    </template>
                                </slide-in>
                                <button class="button small red" @click="$emit('delete', key)">
                                    <icon icon="trash" />
                                </button>
                            </div>
                        </template>
                        <component
                            :is="getFormfieldByType(formfield.type).builder_component"
                            v-bind:options="formfield.options"
                            :class="formfield.options.classes"
                            :column="formfield.column"
                            :columns="columns"
                            action="view" />
                        <p class="description" v-if="translate(formfield.options.description) !== ''">
                            {{ translate(formfield.options.description) }}
                        </p>
                    </card>
                </div>
            </template>
        </draggable>
    </div>
</template>

<script>
import draggable from 'vuedraggable';
import BreadBuilderValidation from './ValidationForm';

export default {
    components: {
        BreadBuilderValidation,
        draggable,
    },
    emits: ['delete', 'update:formfields', 'update:options'],
    props: {
        computed: Array,
        columns: Array,
        relationships: Array,
        formfields: Array,
        options: Object,
        fromRepeater: {
            type: Boolean,
            default: false,
        }
    },
    data() {
        return {
            resizingFormfield: null,
            sizes: [
                'w-1/6',
                'w-2/6',
                'w-3/6',
                'w-4/6',
                'w-5/6',
                'w-full',
            ]
        };
    },
    methods: {
        startResize(key) {
            this.resizingFormfield = key;
        },
        prev(formfield) {
            this.$emit('update:formfields', this.formfields.moveElementUp(formfield));
        },
        next(formfield) {
            this.$emit('update:formfields', this.formfields.moveElementDown(formfield));
        },
    },
    mounted() {
        window.addEventListener('mouseup', () => {
            this.resizingFormfield = null;
        });

        this.$el.addEventListener('mousemove', (e) => {
            if (this.resizingFormfield !== null) {
                e.preventDefault();
                var rect = this.$el.getBoundingClientRect();
                var x = e.clientX - rect.left - 50;
                var threshold = rect.width / (this.sizes.length - 1);
                var size = Math.min(Math.max(Math.ceil(x / threshold), 0), this.sizes.length);
                this.formfields[this.resizingFormfield].options.width = this.sizes[size];
            }
        });
    }
};
</script>