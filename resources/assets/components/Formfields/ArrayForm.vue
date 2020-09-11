<template>
    <div>
        <div class="w-full flex">
            <div class="w-4/6">
                <h5>{{ __(titleText) }}</h5>
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
                        <th>{{ __(itemText) }}</th>
                        <th>{{ __('voyager::generic.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(option, i) in modelValue" :key="'option-'+i">
                        <td>
                            <input type="text" class="input w-full" v-model="value[i]" :placeholder="__(itemText)">
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
</template>
<script>
export default {
    emits: ['update:modelValue'],
    props: {
        modelValue: {
            type: Array,
            required: true,
        },
        titleText: {
            type: String,
            default: function () {
               return 'voyager::generic.options'; 
            }
        },
        itemText: {
            type: String,
            default: function () {
               return 'voyager::generic.item'; 
            }
        },
    },
    methods: {
        addOption: function () {
            this.$emit('update:modelValue', this.modelValue.insert(''));
        },
        removeOption: function (key) {
            this.$emit('update:modelValue', this.modelValue.removeAtIndex(key));
        }
    },
}
</script>