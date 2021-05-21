<template>
    <div v-if="action == 'query'">
        <slot />
    </div>
    <div v-else-if="action == 'browse'" v-html="browseData"></div>
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
        },
        browseData() {
            if (Array.isArray(this.modelValue)) {
                if (this.modelValue.length == 0) {
                    return this.__('voyager::generic.none')
                } else {
                    let data = '';
                    let rows = this.modelValue;
                    let length = this.modelValue.length;
                    let displayRows = (this.options.rows || 3);
                    if (this.options.shuffle) {
                        rows.sort(() => Math.random() - 0.5);
                    }
                    rows.splice(0, (rows.length - displayRows));
                    if (rows[0] && typeof rows[0] === 'object' && rows[0].constructor === Object) {
                        data = rows.map((value) => {
                            let row = Object.keys(value).map((key) => {
                                return this.titleCase(key) + ': ' + value[key];
                            }).join(', ').substr(0, (this.options.length || 15));

                            return row;
                        }).join('<br>');
                    } else {
                        // 1 formfield without key
                        data = rows.map((row) => {
                            if (typeof row === 'string' || row instanceof String) {
                                return row.substr(0, (this.options.length || 15));
                            }
                            return row;
                        }).join('<br>');
                    }

                    if (length > displayRows) {
                        data += '<p class="italic text-sm">'+this.__('voyager::generic.more_results', { num: (length - displayRows) })+'</p>';
                    }

                    return data;
                }

                return JSON.stringify(this.modelValue);
            }

            return this.modelValue;
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
            this.$emit('update:modelValue', model);
        },
        keyForRow(row) {
            return uuidv4();
        }
    },
    created() {
        if (!this.options.formfields) {
            return;
        }

        let fieldsWithoutKey = false;
        this.options.formfields.forEach((formfield) => {
            if (formfield.column.column === null || formfield.column.column === '') {
                fieldsWithoutKey = true;
            }
        });

        if (fieldsWithoutKey && this.options.formfields.length > 1) {
            new this
            .$notification(this.__('voyager::formfields.repeater.key_warning'))
            .color('red')
            .timeout()
            .show();
        }
    },
    data() {
        return {
            bread: {
                name_singular: '',
            },
            layout: {
                formfields: this.options.formfields
            },
        }
    }
}
</script>