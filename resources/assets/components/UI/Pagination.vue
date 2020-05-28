<template>
<div class="button-group">
    <a
        v-if="firstLastButtons"
        @click="selectFirstPage()"
        class="button icon-only"
        :disabled="firstPageSelected"
        :class="[firstPageSelected ? 'disabled' : '', color]">
        {{ __('voyager::generic.first') }}
    </a>

    <a
        v-if="prevNextButtons"
        @click="prevPage()"
        class="button icon-only"
        :class="[firstPageSelected ? 'disabled' : '', color]">
        <icon icon="chevron-left"></icon>
    </a>

    <a
        v-for="(page, i) in pages"
        :key="'page-'+i"
        @click="handlePageSelected(page.index + 1)"
        class="button"
        :class="[page.selected ? 'active' : '', page.disabled ? 'disabled' : '', color]">
        <span
            v-if="page.breakView"
            v-html="breakText">
        </span>
        <span v-else-if="page.disabled">
            {{ page.content }}
        </span>
        <span v-else>
            {{ page.content }}
        </span>
    </a>

    <a
        v-if="prevNextButtons"
        @click="nextPage()"
        class="button icon-only"
        :class="[lastPageSelected ? 'disabled' : '', color]"
        :tabindex="lastPageSelected ? -1 : 0">
        <icon icon="chevron-right"></icon>
    </a>
    <a
        v-if="firstLastButtons"
        @click="selectLastPage()"
        class="button icon-only"
        :class="[lastPageSelected ? 'disabled' : '', color]"
        :tabindex="lastPageSelected ? -1 : 0">
        {{ __('voyager::generic.last') }}
    </a>
</div>
</template>

<script>
export default {
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
        }
    },
    computed: {
        selected: {
            get: function () {
                return this.value || this.innerValue;
            },
            set: function (newValue) {
                this.innerValue = newValue;
            }
        },
        pages: function () {
            let items = {};
            if (this.pageCount <= this.pageRange) {
                for (let index = 0; index < this.pageCount; index++) {
                    let page = {
                        index: index,
                        content: index + 1,
                        selected: index === (this.selected - 1)
                    };
                    items[index] = page;
                }
            } else {
                const halfPageRange = Math.floor(this.pageRange / 2);
                let setPageItem = index => {
                    let page = {
                        index: index,
                        content: index + 1,
                        selected: index === (this.selected - 1)
                    }
                    items[index] = page;
                };
                let setBreakView = index => {
                    let breakView = {
                        disabled: true,
                        breakView: true
                    }
                    items[index] = breakView;
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
            return items
        },
        firstPageSelected: function () {
            return this.selected === 1;
        },
        lastPageSelected: function () {
            return (this.selected === this.pageCount) || (this.pageCount === 0);
        },
    },
    data: function () {
        return {
            innerValue: 1,
        }
    },
    methods: {
        handlePageSelected: function (selected) {
            if (this.selected !== selected && this.isNumber(selected) && selected >= 1) {
                this.innerValue = selected;
                this.$emit('input', selected);
            }
        },
        prevPage: function () {
            if (this.selected > 1) {
                this.handlePageSelected(this.selected - 1);
            }
        },
        nextPage: function () {
            if (this.selected < this.pageCount) {
                this.handlePageSelected(this.selected + 1);
            }
        },
        
        selectFirstPage: function () {
            if (this.selected !== 1) {
                this.handlePageSelected(1);
            }
        },
        selectLastPage: function () {
            if (this.selected !== this.pageCount) {
                this.handlePageSelected(this.pageCount);
            }
        }
    }
}
</script>