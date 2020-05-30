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
            <card :title="translate(formfield.options.title) || ''" :border="formfield.options.border || 'default'">
                <div slot="actions">
                    <button class="button small blue icon-only">
                        <icon icon="arrows-expand" v-sort-handle class="cursor-move" />
                    </button>
                    <button class="button small blue icon-only" @mousedown="startResize(key)">
                        <icon icon="switch-horizontal" class="cursor-move" />
                    </button>
                    <button class="button small blue icon-only" @click="$emit('open-options', key)">
                        <icon icon="cog" />
                    </button>
                    <button class="button small red icon-only" @click="$emit('delete', key)">
                        <icon icon="trash" />
                    </button>
                    <slide-in :opened="optionsId == key" v-on:closed="$emit('open-options', null)" width="w-1/3" class="text-left" :title="__('voyager::generic.options')">
                        <locale-picker v-if="$language.localePicker" slot="actions" />
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
                            <input type="checkbox" class="input" v-model="formfield.translatable">
                        </div>

                        <label class="label mt-4">{{ __('voyager::generic.title') }}</label>
                        <language-input
                            class="input w-full"
                            type="text" :placeholder="__('voyager::generic.title')"
                            v-bind:value="formfield.options.title"
                            v-on:input="formfield.options.title = $event" />

                        <label class="label mt-4">{{ __('voyager::generic.description') }}</label>
                        <language-input
                            class="input w-full"
                            type="text" :placeholder="__('voyager::generic.description')"
                            v-bind:value="formfield.options.description"
                            v-on:input="formfield.options.description = $event" />

                        <component
                            :is="'formfield-'+kebab_case(formfield.type)+'-builder'"
                            v-bind:options="formfield.options"
                            :column="formfield.column"
                            v-bind:relationships="relationships"
                            show="view-options" />
                        <bread-builder-validation v-model="formfield.validation" />

                        <dropdown :ref="'border-'+_uid" pos="right" :width="null">
                            <div class="m-4">
                                <color-picker
                                    v-on:input="$refs['border-'+_uid][0].close(); formfield.options.border = $event"
                                    v-bind:value="formfield.options.border"
                                    palette="tailwind-shades"
                                    :describe="false">
                                </color-picker>
                            </div>
                            <div slot="opener">
                                <button :class="`bg-${formfield.options.border}`" class="button">
                                    {{ __('voyager::builder.border_color') + ': ' + ucfirst(formfield.options.border || 'none') }}
                                </button>
                            </div>
                        </dropdown>
                    </slide-in>
                </div>

                <component
                    :is="'formfield-'+kebab_case(formfield.type)+'-builder'"
                    v-bind:options="formfield.options"
                    :column="formfield.column"
                    show="view" />
                <p class="description" v-if="translate(formfield.options.description) !== ''">
                    {{ translate(formfield.options.description) }}
                </p>
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
        },
        formfields: function (formfields) {
            this.reactiveFormfields = formfields;
        },
        options: function (options) {
            this.reactiveOptions = options;
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
                Vue.set(vm.formfields[vm.resizingFormfield].options, 'width', vm.sizes[size]);
            }
        });
    }
};
</script>