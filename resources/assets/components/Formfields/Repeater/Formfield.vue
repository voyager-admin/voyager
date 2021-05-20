<template>
    <div v-if="action == 'query'">
        <slot />
    </div>
    <div v-else-if="action == 'browse'">
        
    </div>
    <div v-else-if="action == 'edit' || action == 'add'" class="w-full">
        <draggable :list="rows" :item-key="keyForRow" handle=".dd-handle">
            <template #item="{ element: entry, index: key }" :key="key">
                <card :title="`${this.translate(this.options.type, true) || ''} #${key+1}`" :titleSize="6">
                    <template #actions>
                        <div class="flex space-x-1">
                            <button class="button small dd-handle cursor-move">
                                <icon icon="arrows-expand" />
                            </button>
                            <button class="button small red" @click="deleteRow(key)">
                                <icon icon="trash" />
                            </button>
                        </div>
                    </template>
                    <edit-add
                        from-repeater
                        :bread="bread"
                        :layout="layout"
                        :input="entry"
                        @output="setData(key, $event)"
                        action="add"
                    />
                </card>
            </template>
        </draggable>
        
        <button class="button green w-full justify-center" @click="addRow">
            <icon icon="plus-circle" />
            <span>{{ __('voyager::generic.add_type', { type: (this.translate(this.options.type, true) || '') }) }}</span>
        </button>
    </div>
    <div v-else class="flex flex-wrap space-x-1">
        
    </div>
</template>

<script>
import draggable from 'vuedraggable';
import { v4 as uuidv4 } from 'uuid';

import formfield from '../../../js/mixins/formfield';
import EditAdd from '../../Bread/EditAdd';

export default {
    mixins: [formfield],
    components: { EditAdd, draggable },
    computed: {
        rows: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        }
    },
    methods: {
        setData(row, data) {
            let model = this.modelValue;
            model[row] = data;

            this.$emit('update:modelValue', model)
        },
        addRow() {
            this.$emit('update:modelValue', [...this.modelValue, {}])
        },
        deleteRow(row) {
            let model = this.modelValue;
            model.splice(row, 1);
            this.$emit('update:modelValue', model)
        },
        keyForRow(row) {
            return uuidv4();
        }
    },
    data() {
        return {
            bread: {
                name_singular: '',
            },
            layout: {
                formfields: this.options.formfields
            }
        }
    }
}
</script>