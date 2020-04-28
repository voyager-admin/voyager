<template>
    <sort-container
        class="flex flex-wrap w-full"
        tag="div"
        :useDragHandle="true"
        v-model="reactiveFormfields">
        <sort-element
            v-for="(formfield, key) in reactiveFormfields"
            :key="'formfield-'+key"
            :index="key"
            axis="xy"
            class="m-0"
            :class="formfield.options.width">
            <card :title="formfield.column.column || 'No column'">
                <div slot="actions">
                    <button class="button small blue icon-only">
                        <icon icon="expand-arrows" v-sort-handle class="cursor-move" />
                    </button>
                    <button class="button small blue icon-only" @mousedown="startResize(key)">
                        <icon icon="arrows-h" class="cursor-move" />
                    </button>
                    <button class="button small blue icon-only" @click="$emit('open-options', key)">
                        <icon icon="cog" />
                    </button>
                    <button class="button small red icon-only" @click="$emit('delete', key)">
                        <icon icon="trash" />
                    </button>
                    <slide-in :opened="optionsId == key" v-on:closed="$emit('open-options', null)" width="w-1/3" class="text-left">
                        <div class="flex w-full mb-3">
                            <div class="w-1/2 text-2xl">
                                <h4>{{ __('voyager::generic.options') }}</h4>
                            </div>
                            <div class="w-1/2 flex justify-end">
                                <locale-picker v-if="$language.localePicker" />
                                <button class="button green icon-only" @click="$emit('open-options', null)">
                                    <icon icon="times" />
                                </button>
                            </div>
                        </div>
                        <label class="label mt-4">{{ __('voyager::generic.column') }}</label>
                        <select class="voyager-input w-full" v-model="formfield.column">
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
                        <div v-if="formfield.canBeTranslated">
                            <label class="label mt-4">Translatable</label>
                            <input type="checkbox" class="voyager-input" v-model="formfield.translatable">
                        </div>

                        <component
                            :is="'formfield-'+formfield.type+'-builder'"
                            v-bind:options="formfield.options"
                            :column="formfield.column"
                            show="view-options" />
                        <bread-builder-validation v-model="formfield.validation" />
                    </slide-in>
                </div>

                <component
                    :is="'formfield-'+formfield.type+'-builder'"
                    v-bind:options="formfield.options"
                    :column="formfield.column"
                    show="view" />
            </card>
        </sort-element>
        <slot v-if="reactiveFormfields.length == 0" />
    </sort-container>
</template>

<script>
export default {
    props: ['computed', 'columns', 'relationships', 'formfields', 'optionsId', 'options'],
    data: function () {
        return {
            reactiveFormfields: this.formfields,
            reactiveOptions: this.options,
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
        }
    },
    watch: {
        reactiveFormfields: function (formfields) {
            this.$emit('formfields', formfields);
        },
        reactiveOptions: function (options) {
            this.$emit('options', options);
        }
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
                Vue.set(vm.formfields[vm.resizingFormfield].options, 'width', vm.sizes[size]);
            }
        });
    }
};
</script>