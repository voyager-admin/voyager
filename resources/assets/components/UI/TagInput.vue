<template>
    <div class="input" @click="$refs.input.focus()">
        <span>
            <span v-for="(tag, i) in tags" :key="'tag-'+i">
                <badge :color="badgeColor" icon="x" @click-icon="removeTag(tag)" :class="[allowReorder ? 'cursor-move' : '']">
                    <i>{{ tag }}</i>
                </badge>
            </span>
        </span>
        <input type="text" class="bg-transparent border-0 focus:outline-none" ref="input" v-on:keyup.enter="addTag" v-on:keyup.delete="removeLastTag($event)">
    </div>
</template>
<script>
export default {
    emits: ['update:modelValue'],
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