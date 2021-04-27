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
            <template #actions>
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
                            :show-title="translate(formfield.options.label, true) !== ''"
                        >
                            <div>
                                <collapse-transition>
                                    <alert v-if="getErrors(formfield.column).length > 0" color="red" class="mb-2">
                                        <span v-if="getErrors(formfield.column).length == 1">
                                            {{ getErrors(formfield.column)[0] }}
                                        </span>
                                        <ul class="list-disc" v-else>
                                            <li v-for="(error, i) in getErrors(formfield.column)" :key="'error-'+i">
                                                {{ error }}
                                            </li>
                                        </ul>
                                    </alert>
                                </collapse-transition>
                                <component
                                    :is="getFormfieldByType(formfield.type).component"
                                    :modelValue="getData(formfield)"
                                    @update:modelValue="setData(formfield, $event)"
                                    :bread="bread"
                                    :options="formfield.options"
                                    :column="formfield.column"
                                    :relationships="relationships"
                                    :translatable="formfield.translatable"
                                    :from-relationship="fromRelationship"
                                    :action="currentAction"
                                    :primary-key="primaryKey"
                                />
                                <p class="description" v-if="translate(formfield.options.description, true) !== ''">
                                    {{ translate(formfield.options.description, true) }}
                                </p>
                            </div>
                        </card>
                    </div>
                </div>
                <button class="button green" @click="save" :disabled="isSaving">
                    <icon icon="refresh" class="animate-spin-reverse" v-if="isSaving" />
                    <span>{{ __('voyager::generic.save') }}</span>
                </button>
            </div>
        </card>
        <collapsible v-if="!fromRelationship && jsonOutput !== null" :title="__('voyager::builder.json_output')" closed>
            <textarea class="input w-full" rows="10" v-model="jsonOutput"></textarea>
        </collapsible>
    </div>
</template>

<script>
import { usePage } from '@inertiajs/inertia-vue3';
import axios from 'axios';

export default {
    emits: ['saved'],
    props: ['bread', 'action', 'input', 'layout', 'prevUrl', 'relationships', 'fromRelationship', 'primaryKey'],
    data() {
        return {
            output: (this.input || {}),
            isSaving: false,
            isSaved: false,
            errors: [],
            currentAction: this.action,
            id: this.primaryKey
        };
    },
    methods: {
        getData(formfield) {
            if ((formfield.translatable || false) && !this.isObject(this.output[formfield.column.column])) {
                var value = this.output[formfield.column.column];
                this.output[formfield.column.column] = {};
                this.output[formfield.column.column][usePage().props.value.locale] = value;
            }
            
            if (formfield.translatable || false) {
                return this.output[formfield.column.column][usePage().props.value.locale];
            }

            return this.output[formfield.column.column];
        },
        setData(formfield, value) {
            this.getData(formfield);

            if (!this.output.hasOwnProperty(formfield.column.column)) {
                if (formfield.translatable || false) {
                    this.output[formfield.column.column] = {};
                } else {
                    this.output[formfield.column.column] = '';
                }
            }
            if (formfield.translatable || false) {
                this.output[formfield.column.column][usePage().props.value.locale] = value;
            } else {
                this.output[formfield.column.column] = value;
            }
            this.$eventbus.emit('input', {
                column: formfield.column,
                value: value,
            });
        },
        getErrors(column) {
            return this.errors[column.column] || [];
        },
        save() {
            if (this.isSaving) {
                return;
            }
            this.isSaving = true;
            this.isSaved = false;
            let url = (this.currentAction == 'add' ? this.route('voyager.' + this.translate(this.bread.slug, true) + '.store') : this.route('voyager.' + this.translate(this.bread.slug, true) + '.update', this.id));
            let req = axios({
                method: this.currentAction == 'add' ? 'post' : 'put',
                url: url,
                data: this.output
            }, this.id)
            .then((response) => {
                this.errors = [];
                if (this.fromRelationship === true) {
                    this.$emit('saved', {
                        key: response.data,
                        data: this.output
                    });
                    return;
                }

                if (this.currentAction == 'add') {
                    this.currentAction = 'edit';
                    this.id = response.data;

                    new this
                    .$notification(this.__('voyager::bread.type_store_success', {type: this.translate(this.bread.name_singular, true)}))
                    .color('green').timeout().show();
                } else {
                    new this
                    .$notification(this.__('voyager::bread.type_update_success', {type: this.translate(this.bread.name_singular, true)}))
                    .color('green').timeout().show();
                }
            })
            .catch((response) => {
                if (response.status == 422) {
                    // Validation failed
                    this.errors = response.data;
                    new this
                    .$notification(this.__('voyager::bread.validation_errors'))
                    .color('red').timeout().show();
                } else {
                    this.handleAjaxError(response);
                }
            })
            .then(() => {
                this.isSaving = false;
                this.isSaved = true;
            });
        }
    },
    computed: {
        jsonOutput() {
            return usePage().props.value.json_output ? JSON.stringify(this.output, null, 2) : null;
        }
    },
    mounted() {
        $eventbus.on('ctrl-s-combo', () => {
            this.save();
        });

        this.layout.formfields.forEach((formfield) => {
            var value = this.output[formfield.column.column];
            if (formfield.translatable || false) {
                value = this.output[formfield.column.column][usePage().props.value.locale];
            }

            this.$eventbus.emit('input', {
                column: formfield.column,
                value: value,
            });
        })
    }
};
</script>