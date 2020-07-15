<template>
    <div>
        <card :title="__('voyager::generic.'+currentAction+'_type', { type: translate(bread.name_singular, true) })" :icon="bread.icon">
            <div slot="actions">
                    <div class="flex items-center">
                        <a class="button accent ltr:mr-2 rtl:ml-2" v-if="prevUrl !== ''" :href="prevUrl">
                            <span>{{ __('voyager::generic.back') }}</span>
                        </a>
                        <locale-picker v-if="$language.localePicker" :small="false"></locale-picker>
                    </div>
                </div>
                <div>
                    <div class="flex flex-wrap w-full">
                        <div v-for="(formfield, key) in layout.formfields" :key="'formfield-'+key" class="m-0" :class="formfield.options.width">
                            <card
                                :title="translate(formfield.options.title, true)"
                                :title-size="5"
                                :show-title="translate(formfield.options.label, true) !== ''">
                                <div>
                                    <alert v-if="getErrors(formfield.column).length > 0" color="red" :closebutton="false">
                                        <ul class="list-disc ml-4">
                                            <li v-for="(error, i) in getErrors(formfield.column)" :key="'error-'+i">
                                                {{ error }}
                                            </li>
                                        </ul>
                                    </alert>
                                    <component
                                        :is="'formfield-'+kebab_case(formfield.type)+'-edit-add'"
                                        v-bind:value="data(formfield, null)"
                                        v-on:input="data(formfield, $event)"
                                        :options="formfield.options"
                                        :column="formfield.column"
                                        :relationships="relationships"
                                        :show="currentAction" />
                                    <p class="description" v-if="translate(formfield.options.description, true) !== ''">
                                        {{ translate(formfield.options.description, true) }}
                                    </p>
                                </div>
                            </card>
                        </div>
                    </div>
                    <button class="button green" @click="save">
                        <icon icon="refresh" class="rotating-ccw" :size="4" v-if="isSaving" />
                        <span>{{ __('voyager::generic.save') }}</span>
                    </button>
                </div>
        </card>
        <collapsible v-if="!fromRelationship && $store.json_output" :title="__('voyager::builder.json_output')" :opened="false">
            <textarea class="input w-full" rows="10" v-model="jsonOutput"></textarea>
        </collapsible>
    </div>
</template>

<script>
import { EventBus } from '../../js/eventbus';

export default {
    props: ['bread', 'action', 'input', 'layout', 'prevUrl', 'translatable', 'relationships', 'fromRelationship'],
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
        data: function (formfield, value = null) {
            // TODO: if column.type == relationship, this won't work
            if (formfield.translatable || false && this.output[formfield.column.column] && this.isString(this.output[formfield.column.column])) {
                Vue.set(this.output, formfield.column.column, this.get_translatable_object(this.output[formfield.column.column]));
            }
            if (value) {
                if (!this.output.hasOwnProperty(formfield.column.column)) {
                    if (formfield.translatable || false) {
                        Vue.set(this.output, formfield.column.column, {});
                    } else {
                        Vue.set(this.output, formfield.column.column, '');
                    }
                }
                if (formfield.translatable || false) {
                    Vue.set(this.output[formfield.column.column], this.$language.locale, value);
                } else {
                    Vue.set(this.output, formfield.column.column, value);
                }

                EventBus.$emit('input', {
                    column: formfield.column,
                    value: value,
                });
            }
            if (formfield.translatable || false) {
                return this.translate(this.get_translatable_object(this.output[formfield.column.column]));
            }

            return this.input[formfield.column.column];
        },
        getErrors: function (column) {
            // TODO: if column.type == relationship, this won't work
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
            axios({
                method: vm.currentAction == 'add' ? 'post' : 'put',
                url: vm.currentAction == 'add' ? vm.route('voyager.' + vm.translate(vm.bread.slug, true) + '.store') : vm.route('voyager.' + vm.translate(vm.bread.slug, true) + '.update', vm.id),
                data: {
                    data: vm.output
                }
            })
            .then(function (response) {
                if (vm.fromRelationship === true) {
                    vm.$emit('saved', response.data);
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
                if (response.response.status == 422) {
                    // Validation failed
                    vm.errors = response.response.data;
                } else {
                    new vm
                    .$notification(vm.__('voyager::bread.type_save_failed', {type: vm.translate(vm.bread.name_singular, true)}) + '<br><br>' + response.response.data.message)
                    .color('red').timeout().show();
                }
            })
            .finally(function () {
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
        if (vm.translatable) {
            Vue.prototype.$language.localePicker = true;
        }

        document.addEventListener('keydown', function (e) {
            if (event.ctrlKey && event.key === 's') {
                e.preventDefault();
                vm.save();
            }
        });
    }
};
</script>