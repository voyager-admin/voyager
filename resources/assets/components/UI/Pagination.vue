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
        value: {
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
        selected: {
            get: function () {
                return this.value || this.pageValue;
            },
            set: function (newValue) {
                this.pageValue = newValue;
            }
        },
        pages: function () {
            let pages = {};
            if (this.pageCount <= this.pageRange) {
                for (let index = 0; index < this.pageCount; index++) {
                    let page = {
                        index: index,
                        content: index + 1,
                        selected: index === (this.selected - 1)
                    };
                    pages[index] = page;
                }
            } else {
                const halfPageRange = Math.floor(this.pageRange / 2);
                let setPageItem = index => {
                    let page = {
                        index: index,
                        content: index + 1,
                        selected: index === (this.selected - 1)
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
                if (this.selected - halfPageRange > 0) {
                    selectedRangeLow = this.selected - 1 - halfPageRange;
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
        isFirstPage: function () {
            return this.selected === 1;
        },
        isLastPage: function () {
            return (this.selected === this.pageCount) || (this.pageCount === 0);
        },
    },
    data: function () {
        return {
            pageValue: 1,
        }
    },
    methods: {
        selectPage: function (selected) {
            if (this.selected !== selected && this.isNumber(selected) && selected >= 1) {
                this.pageValue = selected;
                this.$emit('update:modelValue', selected);
            }
        },
        prevPage: function () {
            if (this.selected > 1) {
                this.selectPage(this.selected - 1);
            }
        },
        nextPage: function () {
            if (this.selected < this.pageCount) {
                this.selectPage(this.selected + 1);
            }
        },
        
        selectFirstPage: function () {
            if (this.selected !== 1) {
                this.selectPage(1);
            }
        },
        selectLastPage: function () {
            if (this.selected !== this.pageCount) {
                this.selectPage(this.pageCount);
            }
        }
    }
}
</script>