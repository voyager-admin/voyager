<template>
    <div class="input" @click="$refs.input.focus()">
        <sort-container v-model="tags" tag="span" axis="x" :hideSortableGhost="false" :useDragHandle="true">
            <sort-element v-for="(tag, i) in tags" :key="'tag-'+i" :index="i" tag="span" :disabled="!allowReorder">
                <badge :color="badgeColor" icon="x" @click-icon="removeTag(tag)" class="large" :class="[allowReorder ? 'cursor-move' : '']">
                    <span v-sort-handle>{{ tag }}</span>
                </badge>
            </sort-element>
        </sort-container>
        <input type="text" class="bg-transparent border-0 focus:outline-none" ref="input" v-on:keyup.enter="addTag" v-on:keyup.delete="removeLastTag($event)">
    </div>
</template>
<script>
export default {
    props: {
        value: {
            type: Array,
            default: function () {
                return [];
            }
        },
        badgeColor: {
            type: String,
            default: 'accent',
        },
        allowEmpty: {
            type: Boolean,
            default: false,
        },
        allowReorder: {
            type: Boolean,
            default: true,
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
    data: function () {
        return {
            tags: this.value,
            deleteCounter: 0,
        };
    },
    methods: {
        addTag: function (e) {
            if (this.max > 0 && this.tags.length >= this.max) {
                return;
            }
            var content = e.target.value;
            if (!this.allowEmpty && content == '') {
                return;
            }
            if (!this.duplicates && this.tags.indexOf(content) !== -1) {
                e.target.value = '';

                return;
            }
            this.tags.push(content);
            e.target.value = '';
        },
        removeTag: function (tag) {
            if (this.tags.length <= this.min) {
                return;
            }
            this.tags.splice(this.tags.indexOf(tag), 1);
        },
        removeLastTag: function (e) {
            if (e.target.value == '') {
                this.deleteCounter++;
                if (this.deleteCounter >= 2) {
                    this.tags.splice(this.tags.length - 1, 1);
                    this.deleteCounter = 0;
                }
            }
        }
    },
    watch: {
        tags: function (tags) {
            this.$emit('input', tags);
        },
        value: function (tags) {
            this.tags = tags;
        }
    }
};
</script>