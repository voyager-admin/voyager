<template>
    <slot v-if="action == 'query'"></slot>
    <div v-else-if="action == 'browse'" class="flex flex-wrap space-x-1">
        <badge v-for="(tag, i) in modelValue" :key="'tag-'+i">
            {{ tag }}
        </badge>
    </div>
    <div v-else-if="action == 'edit' || action == 'add'">
        <div class="voyager-table">
            <table>
                <thead>
                    <tr>
                        <th>{{ translate(options.item_description, true) || __('voyager::generic.value') }}</th>
                        <th class="flex flex-no-wrap justify-end items-center">
                            <button class="button green small" @click.stop="addOption">
                                <icon icon="plus" />
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(option, i) in (value || [])" :key="'option-'+i">
                        <td>
                            <input
                                class="input small w-full"
                                type="text"
                                :placeholder="__('voyager::generic.value')"
                                v-model="value[i]"
                            />
                        </td>
                        <td class="flex flex-no-wrap justify-end items-center">
                            <button class="button small" @click.prevent.stop="optionUp(option)" v-if="options.reordering">
                                <icon icon="chevron-up" />
                            </button>
                            <button class="button small" @click.prevent.stop="optionDown(option)" v-if="options.reordering">
                                <icon icon="chevron-down" />
                            </button>
                            <button class="button red small" @click.stop="removeOption(option)">
                                <icon icon="trash" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div v-else>
        {{ modelValue }}
    </div>
</template>

<script>
import formfield from '../../../js/mixins/formfield';

export default {
    mixins: [formfield],
    computed: {
        value: {
            get() {
                if (!this.isArray(this.modelValue)) {
                    this.$emit('update:modelValue', []);
                    return [];
                }

                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        },
    },
    methods: {
        addOption() {
            this.$emit('update:modelValue', [...this.value, '']);
            
        },
        removeOption(option) {
            this.value.splice(this.value.indexOf(option), 1);
        },
        optionUp(option) {
            if (this.options.reordering) {
                this.value = this.value.moveElementUp(option);
            }
        },
        optionDown(option) {
            if (this.options.reordering) {
                this.value = this.value.moveElementDown(option);
            }
        }
    },
}
</script>