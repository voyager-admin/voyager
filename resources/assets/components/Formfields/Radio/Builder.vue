<template>
    <div v-if="action == 'list-options' || action == 'view-options'">
        <div v-if="action == 'view-options'">
            <label for="inline" class="label">{{ __('voyager::generic.inline') }}</label>
            <input
                type="checkbox"
                id="inline"
                class="input"
                v-model="options.inline"
            /> 
        </div>
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
    <div v-else-if="action == 'view'" class="w-full" :class="(options.inline || false) ? 'space-x-1.5' : null">
        <template v-for="(option, i) in (options.options || [])" :key="i">
            <div class="inline-flex items-center space-x-1.5" :class="(options.inline || false) ? null : 'w-full'">
                <input type="radio" class="input" :value="option.key" />
                <label class="label">{{ translate(option.value, true) }}</label>
            </div>
        </template>
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