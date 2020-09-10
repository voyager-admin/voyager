<template>
    <div class="mt-5">
        <div class="w-full flex">
            <div class="w-4/6">
                <h5>{{ __('voyager::generic.validation') }}</h5>
            </div>
            <div class="w-2/6 text-right">
                <button class="button green small" @click.stop="addRule">
                    <icon icon="plus" />
                </button>
            </div>
        </div>
        <div class="voyager-table">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('voyager::generic.rule') }}</th>
                        <th>{{ __('voyager::generic.message') }}</th>
                        <th>{{ __('voyager::generic.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(rule, key) in modelValue" :key="'rule-'+key">
                        <td>
                            <input type="text" class="input w-full" v-model="rule.rule" :placeholder="__('voyager::generic.rule')">
                        </td>
                        <td>
                            <language-input
                                class="input w-full"
                                type="text" :placeholder="__('voyager::generic.message')"
                                v-model="rule.message" />
                        </td>
                        <td>
                            <button class="button red small" @click.stop="removeRule(key)">
                                <icon icon="trash" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    emits: ['update:modelValue'],
    props: ['modelValue'],
    methods: {
        addRule: function () {
            var rules = this.modelValue;
            if (!this.isArray(rules)) {
                rules = [];
            }
            rules.push({
                rule: '',
                message: '',
            });

            this.$emit('update:modelValue', rules);
        },
        removeRule: function (key) {
            var rules = this.modelValue;
            this.$emit('update:modelValue', rules.splice(key, 1));
        }
    },
};
</script>