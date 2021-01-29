<template>
    <div v-if="action == 'list-options' || action == 'view-options'">
        <label class="label">{{ __('voyager::generic.multiple') }}</label>
        <input class="input" type="checkbox" v-model="options.multiple" />

        <div class="w-full flex">
            <div class="w-4/6">
                <h5>{{ __('voyager::generic.options') }}</h5>
            </div>
            <div class="w-2/6 text-right">
                <button class="button green small" @click.stop="addOption">
                    <icon icon="plus" />
                </button>
            </div>
        </div>
        <div class="voyager-table">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('voyager::generic.key') }}</th>
                        <th>{{ __('voyager::generic.value') }}</th>
                        <th>{{ __('voyager::generic.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(option, i) in (options.options || [])" :key="'option-'+i">
                        <td>
                            <input type="text" class="input w-full" v-model="option.key" :placeholder="__('voyager::generic.key')">
                        </td>
                        <td>
                            <language-input
                                class="input w-full"
                                type="text"
                                :placeholder="__('voyager::generic.value')"
                                v-model="option.value" />
                        </td>
                        <td>
                            <button class="button red small" @click.stop="removeOption(i)">
                                <icon icon="trash" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div v-else-if="action == 'view'">
        <select class="input w-full">
            <option v-for="(option, i) in (options.options || [])" :key="i" :value="option.key">
                {{ translate(option.value) }}
            </option>
        </select>
    </div>
</template>

<script>
import formfieldBuilder from '../../../js/mixins/formfield-builder';

export default {
    mixins: [formfieldBuilder],
    methods: {
        addOption() {
            var option = {
                key: '',
                value: '',
            };

            var options = this.options;
            if (!this.isArray(this.options.options)) {
                options.options = [];
            }
            options.options = [...this.options.options, option];
            this.$emit('update:options', options);
            
        },
        removeOption(key) {
            this.$emit('update:options', this.options.options.removeAtIndex(key));
        }
    },
}
</script>