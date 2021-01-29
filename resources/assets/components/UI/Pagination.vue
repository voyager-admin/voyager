<template>
<div class="button-group" v-show="pageCount >= 2">
    <a
        v-if="firstLastButtons"
        @click="selectFirstPage()"
        class="button"
        :disabled="isFirstPage"
        :class="[isFirstPage ? 'disabled' : '', color, small ? 'small' : '']"
    >
        {{ __('voyager::generic.first') }}
    </a>

    <a
        v-if="prevNextButtons"
        @click="prevPage()"
        class="button"
        :class="[isFirstPage ? 'disabled' : '', color, small ? 'small' : '']"
    >
        <icon icon="chevron-left"></icon>
    </a>

    <a
        v-for="(page, i) in pages"
        :key="'page-'+i"
        @click="selectPage(page.index + 1)"
        class="button"
        :class="[page.selected ? 'active' : '', page.disabled ? 'disabled' : '', color, small ? 'small' : '']"
    >
        <i
            v-if="page.breakView"
            v-html="breakText">
        </i>
        <i v-else-if="page.disabled">
            {{ page.content }}
        </i>
        <i v-else>
            {{ page.content }}
        </i>
    </a>

    <a
        v-if="prevNextButtons"
        @click="nextPage()"
        class="button"
        :class="[isLastPage ? 'disabled' : '', color, small ? 'small' : '']"
        :tabindex="isLastPage ? -1 : 0"
    >
        <icon icon="chevron-right"></icon>
    </a>
    <a
        v-if="firstLastButtons"
        @click="selectLastPage()"
        class="button"
        :class="[isLastPage ? 'disabled' : '', color, small ? 'small' : '']"
        :tabindex="isLastPage ? -1 : 0"
    >
        {{ __('voyager::generic.last') }}
    </a>
</div>
</template>

<script>
export default {
    emits: ['update:modelValue'],
    props: {
        modelValue: {
            type: Number
        },
        pageCount: {
            type: Number,
            required: true
        },
        pageRange: {
            type: Number,
            default: 3
        },
        marginPages: {
            type: Number,
            default: 1
        },
        breakText: {
            type: String,
            default: '…'
        },
        firstLastButtons: {
            type: Boolean,
            default: true
        },
        prevNextButtons: {
            type: Boolean,
            default: true
        },
        color: {
            type: String,
            default: 'accent'
        },
        small: {
            type: Boolean,
            default: false,
        }
    },
    computed: {
        pages() {
            let pages = {};
            if (this.pageCount <= this.pageRange) {
                for (let index = 0; index < this.pageCount; index++) {
                    let page = {
                        index: index,
                        content: index + 1,
                        selected: index === (this.modelValue - 1)
                    };
                    pages[index] = page;
                }
            } else {
                const halfPageRange = Math.floor(this.pageRange / 2);
                let setPageItem = index => {
                    let page = {
                        index: index,
                        content: index + 1,
                        selected: index === (this.modelValue - 1)
                    }
                    pages[index] = page;
                };
                let setBreakView = index => {
                    let breakView = {
                        disabled: true,
                        breakView: true
                    }
                    pages[index] = breakView;
                };
                for (let i = 0; i < this.marginPages; i++) {
                    setPageItem(i);
                }
                let selectedRangeLow = 0;
                if (this.modelValue - halfPageRange > 0) {
                    selectedRangeLow = this.modelValue - 1 - halfPageRange;
                }
                let selectedRangeHigh = selectedRangeLow + this.pageRange - 1;
                if (selectedRangeHigh >= this.pageCount) {
                    selectedRangeHigh = this.pageCount - 1;
                    selectedRangeLow = selectedRangeHigh - this.pageRange + 1;
                }
                for (let i = selectedRangeLow; i <= selectedRangeHigh && i <= this.pageCount - 1; i++) {
                    setPageItem(i);
                }
                if (selectedRangeLow > this.marginPages) {
                    setBreakView(selectedRangeLow - 1);
                }
                if (selectedRangeHigh + 1 < this.pageCount - this.marginPages) {
                    setBreakView(selectedRangeHigh + 1);
                }
                for (let i = this.pageCount - 1; i >= this.pageCount - this.marginPages; i--) {
                    setPageItem(i);
                }
            }

            return pages;
        },
        isFirstPage() {
            return this.modelValue === 1;
        },
        isLastPage() {
            return (this.modelValue === this.pageCount) || (this.pageCount === 0);
        },
    },
    methods: {
        selectPage(selected) {
            if (this.modelValue !== selected && this.isNumber(selected) && selected >= 1) {
                this.$emit('update:modelValue', selected);
            }
        },
        prevPage() {
            if (this.modelValue > 1) {
                this.selectPage(this.selected - 1);
            }
        },
        nextPage() {
            if (this.modelValue < this.pageCount) {
                this.selectPage(this.modelValue + 1);
            }
        },
        
        selectFirstPage() {
            if (this.modelValue !== 1) {
                this.selectPage(1);
            }
        },
        selectLastPage() {
            if (this.modelValue !== this.pageCount) {
                this.selectPage(this.pageCount);
            }
        }
    }
}
</script>