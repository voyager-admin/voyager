<template>
    <div class="flex flex-wrap w-full">
        <div v-for="(formfield, key) in formfields" :key="'formfield-'+key" class="m-0" :class="formfield.options.width">
            <card :title="translate(formfield.options.title) || ''" :title-size="5">
                <template #actions>
                    <button class="button small">
                        <icon icon="chevron-left" @click.prevent.stop="prev(formfield)" />
                    </button>
                    <button class="button small">
                        <icon icon="chevron-right" @click.prevent.stop="next(formfield)" />
                    </button>
                    <button class="button small" @mousedown="startResize(key)">
                        <icon icon="switch-horizontal" class="cursor-move" />
                    </button>
                    <slide-in :title="__('voyager::generic.options')">
                        <template #actions>
                            <locale-picker />
                        </template>
                        <label class="label mt-4">{{ __('voyager::generic.column') }}</label>
                        <!-- TODO: Hide this if formfield doesn't allow any kind of column -->
                        <select class="input w-full" v-model="formfield.column">
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
                            <template v-for="(relationship, i) in relationships" :key="'relationship_'+i">
                            <optgroup :label="relationship.method" v-if="$store.getFormfieldByType(formfield.type).allowRelationshipColumns">
                                <option v-for="(column, i) in relationship.columns" :key="'column_'+i" :value="{column: relationship.method+'.'+column, type: 'relationship'}">
                                    {{ column }}
                                </option>
                                <template v-for="(column, i) in relationship.pivot" :key="'pivot_'+i">
                                    <option :value="{column: relationship.method+'.pivot.'+column, type: 'relationship'}" v-if="$store.getFormfieldByType(formfield.type).allowPivot">
                                        pivot.{{ column }}
                                    </option>
                                </template>
                            </optgroup>
                            </template>
                            <optgroup v-if="$store.getFormfieldByType(formfield.type).allowRelationships" :label="__('voyager::generic.relationships')">
                                <option v-for="(relationship, i) in relationships" :key="'relationship_'+i" :value="{column: relationship.method, type: 'relationship'}">
                                    {{ relationship.method }}
                                </option>
                            </optgroup>
                        </select>
                        <div v-if="formfield.canBeTranslated">
                            <label class="label mt-4">Translatable</label>
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
                            :is="'formfield-'+kebabCase(formfield.type)+'-builder'"
                            v-bind:options="formfield.options"
                            :column="formfield.column"
                            v-bind:relationships="relationships"
                            show="view-options" />
                        <bread-builder-validation v-model="formfield.validation" />

                        <template #opener>
                            <button class="button small">
                                <icon icon="cog" />
                            </button>
                        </template>
                    </slide-in>
                    <button class="button small red" @click="$emit('delete', key)">
                        <icon icon="trash" />
                    </button>
                </template>

                <component
                    :is="'formfield-'+kebabCase(formfield.type)+'-builder'"
                    v-bind:options="formfield.options"
                    :column="formfield.column"
                    show="view" />
                <p class="description" v-if="translate(formfield.options.description) !== ''">
                    {{ translate(formfield.options.description) }}
                </p>
            </card>
        </div>
        <slot v-if="formfields.length == 0" />
    </div>
</template>

<script>
export default {
    emits: ['delete', 'update:formfields', 'update:options'],
    props: ['computed', 'columns', 'relationships', 'formfields', 'options'],
    data: function () {
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
        startResize: function (key) {
            this.resizingFormfield = key;
        },
        prev: function (formfield) {
            this.$emit('update:formfields', this.formfields.moveElementUp(formfield));
        },
        next: function (formfield) {
            this.$emit('update:formfields', this.formfields.moveElementDown(formfield));
        },
    },
    mounted: function () {
        var vm = this;

        window.addEventListener('mouseup', function () {
            vm.resizingFormfield = null;
        });

        this.$el.addEventListener('mousemove', function (e) {
            if (vm.resizingFormfield !== null) {
                e.preventDefault();
                var rect = vm.$el.getBoundingClientRect();
                var x = e.clientX - rect.left - 50;
                var threshold = rect.width / (vm.sizes.length - 1);
                var size = Math.min(Math.max(Math.ceil(x / threshold), 0), vm.sizes.length);
                vm.formfields[vm.resizingFormfield].options.width = vm.sizes[size];
            }
        });
    }
};
</script>