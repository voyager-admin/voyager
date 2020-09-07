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
                    <tr v-for="(option, i) in value" :key="'option-'+i">
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
    data: function () {
        return {
            value: this.modelValue,
        };
    },
    methods: {
        addOption: function () {
            if (!this.isArray(this.modelValue)) {
                this.value = [];
            }
            this.value.push('');
        },
        removeOption: function (key) {
            this.value.splice(key, 1);
        }
    },
    watch: {
        value: function (options) {
            this.$emit('update:modelValue', options);
        }
    }
}
</script>