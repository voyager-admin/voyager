<template>
    <div class="input flex flex-wrap space-x-1" @click="$refs.input.focus()">
        <component :is="!noReorder ? 'draggable' : 'span'" v-model="tags" class="flex flex-wrap space-x-1" item-key="">
            <template #item="{ element: tag }">
                <badge :color="badgeColor" icon="x" @click-icon="removeTag(tag)" :class="[!noReorder ? 'cursor-move' : '']">
                    {{ tag }}
                </badge>
            </template>
        </component>
        <input type="text" class="bg-transparent border-0 focus:outline-none flex-grow" ref="input" v-on:keyup.enter="addTag" v-on:keyup.delete="removeLastTag($event)">
    </div>
</template>
<script>
import draggable from 'vuedraggable';

export default {
    emits: ['update:modelValue'],
    components: { draggable },
    props: {
        modelValue: {
            type: Array,
            default: () => {
                return [];
            }
        },
        badgeColor: {
            type: String,
            default: 'accent',
        },
        empty: {
            type: Boolean,
            default: false,
        },
        noReorder: {
            type: Boolean,
            default: false,
        },
        min: {
            type: Number,
            default: 0,
        },
        max: {
            type: Number,
            default: 0,
        },
        duplicates: {
            type: Boolean,
            default: false,
        }
    },
    data() {
        return {
            tags: this.modelValue,
            deleteCounter: 0,
        };
    },
    methods: {
        addTag(e) {
            if (this.max > 0 && this.tags.length >= this.max) {
                return;
            }
            var content = e.target.value;
            if (!this.empty && content == '') {
                return;
            }
            if (!this.duplicates && this.tags.indexOf(content) !== -1) {
                e.target.value = '';

                return;
            }
            this.tags.push(content);
            e.target.value = '';
        },
        removeTag(tag) {
            if (this.tags.length <= this.min) {
                return;
            }
            this.tags.splice(this.tags.indexOf(tag), 1);
        },
        removeLastTag(e) {
            if (e.target.value == '') {
                this.deleteCounter++;
                if (this.deleteCounter >= 2) {
                    this.tags.splice(this.tags.length - 1, 1);
                    this.deleteCounter = 0;
                }
            }
        }
    },
    created() {
        this.$watch(() => this.tags, (tags) => {
            this.$emit('update:modelValue', tags);
        });
        this.$watch(() => this.modelValue, (tags) => {
            this.tags = tags;
        });
    },
};
</script>