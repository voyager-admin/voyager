<template>
    <div>
        <card
            :title="__('voyager::generic.'+currentAction+'_type', { type: translate(bread.name_singular, true) })"
            :class="fromRelationship ? 'border-none' : null"
            :style="fromRelationship ? 'box-shadow: none !important' : null"
            :icon="bread.icon"
            :dontShowHeader="fromRelationship"
            :no-padding="fromRelationship"
        >
            <template v-slot:actions>
                <div class="flex items-center">
                    <a class="button small" v-if="prevUrl !== ''" :href="prevUrl">
                        <icon icon="chevron-left"></icon>
                        <span>{{ __('voyager::generic.back') }}</span>
                    </a>
                    <locale-picker class="ltr:mr-2 rtl:ml-2"></locale-picker>
                </div>
            </template>
            <div>
                <div class="flex flex-wrap w-full">
                    <div v-for="(formfield, key) in layout.formfields" :key="'formfield-'+key" class="m-0 w-full" :class="'md:' + formfield.options.width">
                        <card
                            :title="translate(formfield.options.title, true)"
                            :title-size="5"
                            :show-title="translate(formfield.options.label, true) !== ''">
                            <div>
                                <alert v-if="getErrors(formfield.column).length > 0" color="red" class="mb-1">
                                    <span v-if="getErrors(formfield.column).length == 1">
                                        {{ getErrors(formfield.column)[0] }}
                                    </span>
                                    <ul class="list-disc" v-else>
                                        <li v-for="(error, i) in getErrors(formfield.column)" :key="'error-'+i">
                                            {{ error }}
                                        </li>
                                    </ul>
                                </alert>
                                <component
                                    :is="'formfield-'+kebab_case(formfield.type)+'-edit-add'"
                                    :model-value="getData(formfield)"
                                    @update:model-value="setData(formfield, $event)"
                                    :bread="bread"
                                    :options="formfield.options"
                                    :column="formfield.column"
                                    :relationships="relationships"
                                    :translatable="formfield.translatable"
                                    :from-relationship="fromRelationship"
                                    :show="currentAction"
                                    :primary-key="primaryKey" />
                                <p class="description" v-if="translate(formfield.options.description, true) !== ''">
                                    {{ translate(formfield.options.description, true) }}
                                </p>
                            </div>
                        </card>
                    </div>
                </div>
                <button class="button green" @click="save">
                    <icon icon="refresh" class="animate-spin-reverse" :size="4" v-if="isSaving" />
                    <span>{{ __('voyager::generic.save') }}</span>
                </button>
            </div>
        </card>
        <collapsible v-if="!fromRelationship && $store.json_output" :title="__('voyager::builder.json_output')" closed>
            <textarea class="input w-full" rows="10" v-model="jsonOutput"></textarea>
        </collapsible>
    </div>
</template>

<script>
import fetch from '../../js/fetch';

export default {
    emits: ['saved'],
    props: ['bread', 'action', 'input', 'layout', 'prevUrl', 'relationships', 'fromRelationship', 'primaryKey'],
    data: function () {
        return {
            output: (this.input || {}),
            isSaving: false,
            isSaved: false,
            errors: [],
            currentAction: this.action,
            id: this.input.id
        };
    },
    methods: {
        getData: function (formfield) {
            if (formfield.translatable || false && this.output[formfield.column.column] && this.isString(this.output[formfield.column.column])) {
                // TODO: Vue.set(this.output, formfield.column.column, this.get_translatable_object(this.output[formfield.column.column]));
                this.output[formfield.column.column] = this.get_translatable_object(this.output[formfield.column.column]);
            }
            
            if (formfield.translatable || false) {
                return this.translate(this.get_translatable_object(this.output[formfield.column.column]));
            }

            return this.output[formfield.column.column];
        },
        setData: function (formfield, value) {
            this.getData(formfield);

            if (!this.output.hasOwnProperty(formfield.column.column)) {
                if (formfield.translatable || false) {
                    // TODO: Vue.set(this.output, formfield.column.column, {});
                    this.output[formfield.column.column] = {};
                } else {
                    // TODO: Vue.set(this.output, formfield.column.column, '');
                    this.output[formfield.column.column] = '';
                }
            }
            if (formfield.translatable || false) {
                // TODO: Vue.set(this.output[formfield.column.column], this.$store.locale, value);
                this.output[formfield.column.column][this.$store.locale] = value;
            } else {
                // TODO: Vue.set(this.output, formfield.column.column, value);
                this.output[formfield.column.column] = value;
            }
            // TODO
            this.$eventbus.emit('input', {
                column: formfield.column,
                value: value,
            });
        },
        getErrors: function (column) {
            return this.errors[column.column] || [];
        },
        save: function () {
            var vm = this;
            if (vm.isSaving) {
                return;
            }
            vm.isSaving = true;
            vm.isSaved = false;
            vm.errors = [];
            fetch.createRequest(
                (vm.currentAction == 'add' ? vm.route('voyager.' + vm.translate(vm.bread.slug, true) + '.store') : vm.route('voyager.' + vm.translate(vm.bread.slug, true) + '.update', vm.id)),
                (vm.currentAction == 'add' ? 'post' : 'put'),
                { data: vm.output }
            )
            .then(function (response) {
                if (vm.fromRelationship === true) {
                    vm.$emit('saved', {
                        key: response.data,
                        data: vm.output
                    });
                    return;
                }

                if (vm.currentAction == 'add') {
                    vm.currentAction = 'edit';
                    vm.id = response.data;

                    new vm
                    .$notification(vm.__('voyager::bread.type_store_success', {type: vm.translate(vm.bread.name_singular, true)}))
                    .color('green').timeout().show();
                } else {
                    new vm
                    .$notification(vm.__('voyager::bread.type_update_success', {type: vm.translate(vm.bread.name_singular, true)}))
                    .color('green').timeout().show();
                }
            })
            .catch(function (response) {
                if (response.status == 422) {
                    // Validation failed
                    vm.errors = response.data;
                    new vm
                    .$notification(vm.__('voyager::bread.validation_errors'))
                    .color('red').timeout().show();
                } else {
                    vm.$store.handleAjaxError(response);
                }
            })
            .then(function () {
                vm.isSaving = false;
                vm.isSaved = true;
            });
        }
    },
    computed: {
        jsonOutput: function () {
            return JSON.stringify(this.output, null, 2);
        }
    },
    mounted: function () {
        var vm = this;

        $eventbus.on('ctrl-s-combo', function () {
            vm.save();
        });
    }
};
</script>