<template>
    <div v-if="action == 'view-options'">
        <div class="input-group mt-2">
            <label class="label mt-4">{{ __('voyager::generic.type') }}</label>
            <language-input
                class="input w-full"
                type="text" :placeholder="__('voyager::generic.type')"
                v-model="options.type" />
        </div>
        <div class="input-group mt-2">
            <label class="label mt-4">{{ __('voyager::formfields.repeater.allow_sort') }}</label>
            <input type="checkbox" class="input" v-model="options.sort">
        </div>
    </div>
    <div v-else-if="action == 'list-options'">
        <div class="input-group mt-2">
            <label class="label mt-4">{{ __('voyager::generic.rows') }}</label>
            <input
                class="input w-full"
                type="number" min="0" :placeholder="__('voyager::generic.rows')"
                v-model.number="options.rows" />
        </div>
        <div class="input-group mt-2">
            <label class="label mt-4">{{ __('voyager::formfields.repeater.chars_per_rows') }}</label>
            <input
                class="input w-full"
                type="number" min="0" :placeholder="__('voyager::formfields.repeater.chars_per_rows')"
                v-model.number="options.length" />
        </div>
        <div class="input-group mt-2">
            <label class="label mt-4">{{ __('voyager::formfields.repeater.shuffle_rows') }}</label>
            <input type="checkbox" class="input" v-model="options.shuffle">
        </div>
    </div>
    <div v-else-if="action == 'view'" class="w-full">
        <alert v-if="keyWarning" color="yellow" class="mb-2">
            {{ __('voyager::formfields.repeater.key_warning') }}
        </alert>
        <card>
            <bread-builder-view
                :computed="[]"
                :columns="[]"
                :relationships="[]"
                v-model:formfields="formfields"
                v-model:options="dummyOptions"
                v-on:delete="deleteFormfield($event)"
                from-repeater
            />
        </card>
    </div>
</template>

<script>
import formfieldBuilder from '../../../js/mixins/formfield-builder';
import BreadBuilderView from '../../Builder/View.vue';

export default {
    mixins: [formfieldBuilder],
    components: { BreadBuilderView },
    computed: {
        defaultViewOptions() {
            return {
                formfields: [],
                type: '',
                sort: true,
            };
        },
        defaultListOptions() {
            return {
                rows: 3,
                length: 15,
                shuffle: false,
            };
        },
        formfields: {
            get() {
                return this.options.formfields || [];
            },
            set(formfields) {
                let o = this.options;
                o.formfields = formfields;

                this.$emit('update:options', o);
            }
        }
    },
    methods: {
        deleteFormfield(key) {
            new this
            .$notification(this.__('voyager::builder.delete_formfield_confirm'))
            .color('yellow')
            .timeout()
            .confirm()
            .show()
            .then((result) => {
                if (result) {
                    let o = this.options;
                    let f = this.options.formfields;
                    f.splice(key, 1);
                    o.formfields = f;
                    this.$emit('update:options', o);
                }
            });
        },
    },
    created() {
        this.$watch(() => this.options.formfields, (formfields) => {
            if (!Array.isArray(formfields)) {
                return;
            }
            let fieldsWithoutKey = false;
            formfields.forEach((formfield) => {
                if (formfield.column.column === null || formfield.column.column === '') {
                    fieldsWithoutKey = true;
                }
            });

            this.keyWarning = (fieldsWithoutKey && this.options.formfields.length > 1);
        }, { immediate: true, deep: true });
    },
    data() {
        return {
            dummyOptions: [],
            keyWarning: false,
        };
    }
}
</script>