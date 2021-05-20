<template>
    <div v-if="action == 'list-options' || action == 'view-options'">
        <label class="label mt-4">{{ __('voyager::generic.type') }}</label>
        <language-input
            class="input w-full"
            type="text" :placeholder="__('voyager::generic.type')"
            v-model="options.type" />

        <label class="label mt-4">{{ __('voyager::formfields.repeater.allow_sort') }}</label>
        <input type="checkbox" class="input" v-model="options.sort">
    </div>
    <div v-else-if="action == 'view'" class="w-full">
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
import BreadBuilderView from '../../Builder/View';

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
            return {};
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
    data() {
        return {
            dummyOptions: []
        };
    }
}
</script>