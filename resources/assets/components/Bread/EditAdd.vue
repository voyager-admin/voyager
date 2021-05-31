<template>
    <div>
        <card
            :dontShowHeader="fromRepeater"
            :title="__('voyager::generic.'+currentAction+'_type', { type: translate(bread.name_singular, true) })"
            :class="fromRepeater ? 'border-none' : null"
            :style="fromRepeater ? 'box-shadow: none !important' : null"
            :icon="bread.icon"
            :no-padding="fromRepeater"
        >
            <template #actions v-if="true">
                <div class="flex items-center space-x-2">
                    <a class="button small" v-if="prevUrl !== ''" :href="prevUrl">
                        <icon icon="chevron-left" />
                        <span>{{ __('voyager::generic.back') }}</span>
                    </a>
                    <locale-picker />
                </div>
            </template>
            <div>
                <div class="flex flex-wrap w-full">
                    <div
                        v-for="(formfield, key) in layout.formfields"
                        :key="'formfield-'+key"
                        class="m-0 w-full"
                        :class="'md:' + formfield.options.width"
                        uses="md:w-1/6 md:w-2/6 md:w-3/6 md:w-4/6 md:w-5/6 md:w-full"
                    >
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
                                    :errors="getErrors(formfield.column)"
                                    :bread="bread"
                                    :options="formfield.options"
                                    :column="formfield.column"
                                    :relationships="relationships"
                                    :translatable="formfield.translatable"
                                    :from-repeater="fromRepeater"
                                    :action="currentAction"
                                    :primary-key="primaryKey"
                                    :class="formfield.options.classes"
                                />
                                <p class="description" v-if="translate(formfield.options.description, true) !== ''">
                                    {{ translate(formfield.options.description, true) }}
                                </p>
                            </div>
                        </card>
                    </div>
                </div>
                <button class="button green space-x-0" @click="save" :disabled="isSaving" v-if="!fromRepeater">
                    <icon icon="refresh" class="animate-spin-reverse" :size="isSaving ? 4 : 0" :transition-size="4" />
                    <span>{{ __('voyager::generic.save') }}</span>
                </button>
            </div>
        </card>
        <collapsible v-if="!fromRepeater && jsonOutput" :title="__('voyager::generic.json_output')" closed>
            <json-editor v-model="output" />
        </collapsible>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    emits: ['saved', 'output'],
    props: {
        bread: Object,
        action: String,
        input: Object,
        layout: Object,
        prevUrl: String,
        relationships: Array,
        primaryKey: [String, Number, Object],
        fromRepeater: {
            type: Boolean,
            default: false,
        }
    },
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
                this.output[formfield.column.column][this.$store.locale] = value;
            }
            
            if (formfield.translatable || false) {
                return this.output[formfield.column.column][this.$store.locale];
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
                this.output[formfield.column.column][this.$store.locale] = value;
            } else {
                this.output[formfield.column.column] = value;
            }
            this.$eventbus.emit('input', {
                column: formfield.column,
                value: value,
            });
            this.$eventbus.emit('output', this.output);
            this.$emit('output', this.output);
        },
        getErrors(column) {
            return this.errors[column.column] || [];
        },
        save(e = null) {
            if (this.isSaving || this.fromRepeater) {
                return;
            }
            if (typeof e === 'object' && e instanceof KeyboardEvent) {
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                } else {
                    return;
                }
            }
            this.isSaving = true;
            this.isSaved = false;
            let url = (this.currentAction == 'add' ? this.route('voyager.' + this.translate(this.bread.slug, true) + '.store') : this.route('voyager.' + this.translate(this.bread.slug, true) + '.update', this.id));
            let req = axios({
                method: this.currentAction == 'add' ? 'post' : 'put',
                url: url,
                data: {
                    data: this.output,
                }
            }, this.id)
            .then((response) => {
                this.errors = [];
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
                if (response.response.status == 422) {
                    this.errors = response.response.data;
                    new this.$notification(this.__('voyager::bread.validation_errors')).color('red').timeout().show();
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
            return this.$store.jsonOutput;
        }
    },
    mounted() {
        document.addEventListener('keydown', this.save);

        this.layout.formfields.forEach((formfield) => {
            var value = this.output[formfield.column.column];
            if (formfield.translatable || false) {
                value = this.output[formfield.column.column][this.$store.locale];
            }

            this.$eventbus.emit('input', {
                column: formfield.column,
                value: value,
            });
        });
    },
    unmounted() {
        document.removeEventListener('keydown', this.save);
    }
};
</script>