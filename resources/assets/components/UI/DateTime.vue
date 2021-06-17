<template>
    <div>
        <component :is="inline ? 'card' : 'dropdown'" prevent no-header ref="dropdown" :placement="placement">
            <div :class="{ 'p-2': !inline }">
                <template v-if="type.includes('date')">
                    <!-- Header -->
                    <div class="flex space-x-2 mx-2 items-center">
                        <button class="button" @click="previousMonth" :disabled="!previousMonthPossible">
                            <icon icon="arrow-circle-left" :size="5" />
                        </button>
                        <div class="flex-grow">
                            <select class="input small w-full" :value="shownDate.month()" @change="shownDate = shownDate.month(parseInt($event.target.value))">
                                <option v-for="month in monthsRange" :key="`month-${month}`" :value="month">
                                    {{ monthNames[month] }}
                                </option>
                            </select>
                        </div>
                        <div class="flex-grow">
                            <select class="input small w-full" :value="shownDate.year()" @change="shownDate = shownDate.year(parseInt($event.target.value))">
                                <option v-for="year in yearsRange" :key="`year-${year}`" :value="year">
                                    {{ year }}
                                </option>
                            </select>
                        </div>
                        <button class="button" @click="nextMonth" :disabled="!nextMonthPossible">
                            <icon icon="arrow-circle-right" />
                        </button>
                    </div>

                    <!-- Weekdays -->
                    <div class="grid grid-cols-7 space-x-1 text-center">
                        <div v-for="day in weekDays" :key="`weekday-${day}`">
                            {{ day }}
                        </div>
                    </div>

                    <!-- Calendar -->
                    <div class="grid grid-cols-7 text-center">
                        <div
                            v-for="date in daysRange"
                            :key="`calendar-${date.month()}-${date.date()}`"
                            class="element"
                            :class="getDateClasses(date)"
                            @click="isDatePossible(date) && pickDate(date)"
                        >
                            {{ date.date() }}
                        </div>
                    </div>
                </template>
                <template v-if="type.includes('time')">
                    <div class="w-full inline-flex space-x-1">
                        <input type="number" class="input w-full small" :value="padTime(date.hour())" @input="date = date.hour(parseInt($event.target.value))" min="0" max="23">
                        <input type="number" class="input w-full small" :value="padTime(date.minute())" @input="date = date.minute(parseInt($event.target.value))" min="0" max="59">
                        <input type="number" class="input w-full small" :value="padTime(date.minute())" @input="date = date.minute(parseInt($event.target.value))" min="0" max="59" v-if="type.includes('second')">
                    </div>
                </template>
                <template v-if="type.includes('year')">
                    <div class="grid grid-cols-3 gap-4">
                        <div
                            v-for="year in yearsRange"
                            :key="`year-${year}`"
                            class="element"
                            :class="getYearClasses(year)"
                            @click="isYearPossible(year) && pickYear(year)"
                        >
                            {{ year }}
                        </div>
                    </div>
                </template>
                <template v-if="type.includes('month')">
                    <div class="grid grid-cols-3 gap-4">
                        <div
                            v-for="month in monthsRange"
                            :key="`month-${month}`"
                            class="element"
                            :class="getMonthClasses(month)"
                            @click="isMonthPossible(month) && pickMonth(month)"
                        >
                            {{ monthNames[month] }}
                        </div>
                    </div>
                </template>
            </div>

            <template v-if="!inline" #opener>
                <div class="inline-flex w-full space-x-1">
                    <input type="text" class="input w-full mt-2" v-model="textModel" :class="{ error: dateInvalid }" />
                </div>
            </template>
            <div v-if="inline" class="inline-flex w-full space-x-1">
                <input type="text" class="input w-full mt-2" v-model="textModel" :class="{ error: dateInvalid }" />
            </div>
        </component>
    </div>
</template>

<script>
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc';
import timezone from 'dayjs/plugin/timezone';
import isBetween from 'dayjs/plugin/isBetween';
import isSameOrAfter from 'dayjs/plugin/isSameOrAfter';
import isSameOrBefore from 'dayjs/plugin/isSameOrBefore';
import customParseFormat from 'dayjs/plugin/customParseFormat';

import { placements } from '@popperjs/core/lib/enums';

const FORMATS = {
    //year: 'YYYY',
    //month: 'MM',
    date: 'YYYY-MM-DD',
    date_time: 'YYYY-MM-DD HH:mm',
    date_time_am_pm: 'YYYY-MM-DD hh:mm A',
    date_time_seconds: 'YYYY-MM-DD HH:mm:ss',
    date_time_am_pm_seconds: 'YYYY-MM-DD hh:mm:ss A',
};

export { FORMATS, dayjs };

export default {
    emits: ['update:modelValue'],
    props: {
        past: {
            type: [String, Number, null],
            default: null,
        },
        future: {
            type: [String, Number, null],
            default: null,
        },
        modelValue: {
            type: [String, null],
            default: null
        },
        type: {
            type: String,
            default: 'date',
            validator: (value) => Object.keys(FORMATS).includes(value),
        },
        inline: {
            type: Boolean,
            default: false,
        },
        displayFormat: {
            type: String,
            default: 'YYYY-MM-DD',
        },
        dayNames: {
            type: Array,
            default: () => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            validator: (value) => value.length == 7,
        },
        monthNames: {
            type: Array,
            default: () => ['January', 'February', 'March', 'April', 'May', 'June', 'Juli', 'August', 'September', 'October', 'November', 'December'],
            validator: (value) => value.length == 12,
        },
        closeOnSelect: {
            type: Boolean,
            default: false,
        },
        sundayFirst: {
            type: Boolean,
            default: false,
        },
        rangeValue: {
            type: Object,
            default: () => {},
        },
        datePossibleCallback: {
            type: Function,
            default: () => true,
        },
        placement: {
            type: String,
            default: 'bottom',
            validator: (value) => {
                return placements.includes(value);
            }
        },
    },
    data() {
        return {
            date: null, // dayjs object generated from "modelValue"
            shownDate: dayjs(), // dayjs object representing the currently shown month and year
            datePast: null, // Dates before this date can not be picked. Generated from prop "past"
            dateFuture: null, // Dates after this date can not be picked. Generated from prop "future"

            dateInvalid: false,
        };
    },
    computed: {
        yearsRange() {
            let years = [];
            for (let y = this.datePast.year(); y <= this.dateFuture.year(); y++) {
                years.push(y);
            }

            return years;
        },
        monthsRange() {
            let months = [];

            for (let m = 0; m < 12; m++) {
                // If the month is before the current month
                if (this.shownDate.month(m).isBefore(dayjs(), 'day')) {
                    // If the last day in that month is possible
                    if (this.isDatePossible(this.shownDate.month(m).date(this.shownDate.daysInMonth()))) {
                        months.push(m);
                    }
                } else {
                    // If the first day in that month is possible
                    if (this.isDatePossible(this.shownDate.month(m).date(0))) {
                        months.push(m);
                    }
                }
            }

            return months;
        },
        weekDays() {
            if (this.sundayFirst) {
                return [
                    this.dayNames[6],
                    ...this.dayNames.slice(0, 6),
                ];
            }

            return this.dayNames;
        },
        daysRange() {
            let days = [];
            let firstDayOfCurrentMonth = dayjs(this.shownDate.date(0));
            let start = (this.sundayFirst ? 0 : 1) - firstDayOfCurrentMonth.day();
            let end = 42 + start;
            
            for (let i = start; i < end; i++) {
                days.push(firstDayOfCurrentMonth.add(i, 'day'));
            }
            
            return days;
        },
        textModel: {
            get() {
                return this.date.format(this.displayFormat);
            },
            set(value) {
                let date = dayjs(value, this.displayFormat, true);
                if (date.isValid() && this.isDatePossible(date)) {
                    this.date = date;
                    this.dateInvalid = false;
                    this.shownDate = date;
                } else {
                    this.dateInvalid = true;
                }
            }
        },
        previousMonthPossible() {
            // Is the last day of the previous month possible
            return this.isDatePossible(this.shownDate.date(1).subtract(1, 'day'));
        },
        nextMonthPossible() {
            // Is the first day of the next day possible
            return this.isDatePossible(this.shownDate.date(this.shownDate.daysInMonth()).add(1, 'day'));
        },
        isRange() {
            return dayjs.isDayjs(this.rangeValue);
        }
    },
    methods: {
        pickDate(date) {
            if (this.isDatePossible(date)) {
                this.date = date;
                this.shownDate = date;

                if (!this.inline && this.closeOnSelect) {
                    this.$refs.dropdown.close();
                }
            }
        },
        pickMonth(month) {

        },
        pickYear(year) {

        },
        isDatePossible(date) {
            return date.isBetween(this.datePast, this.dateFuture, 'day', '[]') && this.datePossibleCallback(date);
        },
        isMonthPossible(month) {
            return true;
        },
        isYearPossible(year) {
            return true;
        },
        getDateClasses(date) {
            let classes = [];
            // Date is not possible
            if (!this.isDatePossible(date)) {
                classes.push('not-possible');
            }

            // Picked date
            if (date.isSame(this.date, 'day')) {
                classes.push('picked');
            }

            // Is in range
            if (this.isRange && date.isBetween(this.date, this.rangeValue, 'day', '[]')) {
                classes.push('range');

                if (!this.date.isSame(this.rangeValue, 'day')) {
                    if (date.isSame(this.date, 'day')) {
                        classes.push(this.date.isBefore(this.rangeValue, 'day') ? 'start' : 'end');
                    } else if (date.isSame(this.rangeValue, 'day')) {
                        classes.push(this.date.isBefore(this.rangeValue, 'day') ? 'end' : 'start');
                    }
                }
            }

            // Not the current month
            if (!date.isBetween(dayjs(this.shownDate.date(0)), dayjs(this.shownDate.date(0).add(this.shownDate.daysInMonth()+1, 'day')), 'day', '()')) {
                classes.push('different-month');
            }

            // Today
            if (date.isSame(dayjs, 'day')) {
                classes.push('today');
            }

            return classes;
        },
        getMonthClasses(month) {

        },
        getYearClasses(year) {

        },
        sanitizeDate(date) {
            if (this.type.includes('year')) {
                date = date.startOf('year');
            } else if (this.type.includes('month')) {
                date = date.startOf('month');
            } else if (!this.type.includes('time')) {
                date = date.startOf('day');
            } else if (!this.type.includes('seconds')) {
                date = date.startOf('minute');
            }

            return date.startOf('second');
        },
        previousMonth() {
            if (this.previousMonthPossible) {
                this.shownDate = this.shownDate.date(1).subtract(1, 'month');
            }
        },
        nextMonth() {
            if (this.nextMonthPossible) {
                this.shownDate = this.shownDate.date(1).add(1, 'month');
            }
        },
        padTime(input) {
            if (input <= 9) {
                return '0' + input;
            }

            return input;
        }
    },
    created: function () {
        dayjs.extend(utc);
        dayjs.extend(timezone);
        dayjs.extend(isBetween);
        dayjs.extend(isSameOrAfter);
        dayjs.extend(isSameOrBefore);
        dayjs.extend(customParseFormat);

        // Watch the incoming "modelValue"
        this.$watch(() => this.modelValue, (value, old) => {
            let date = dayjs(value || undefined);

            if (date.isValid()) {
                this.date = this.sanitizeDate(date);
                this.shownDate = date;
            } else {
                this.shownDate = dayjs();
            }
        }, { immediate: true });

        // Watch internal "date"
        this.$watch(() => this.date, (value, old) => {
            value !== old && this.$emit('update:modelValue', value.tz(dayjs.tz.guess()).toISOString());
        }, { deep: true });

        // Watch incoming "past"
        this.$watch(() => this.past, (value) => {
            if (typeof value === 'number') {
                this.datePast = dayjs().subtract(value, 'day');
            } else if (typeof value === 'string') {
                if (dayjs(value).isValid() && dayjs(value).isSameOrBefore(dayjs)) {
                    this.datePast = dayjs(value);
                } else {
                    console.warn(`Property "past" has an invalid string value "${value}" or is after today`);
                    this.datePast = dayjs().subtract(2, 'year');
                }
            } else {
                this.datePast = dayjs().subtract(2, 'year');
            }
        }, { immediate: true });

        // Watch incoming "future"
        this.$watch(() => this.future, (value) => {
            if (typeof value === 'number') {
                this.dateFuture = dayjs().add(value, 'day');
            } else if (typeof value === 'string') {
                if (dayjs(value).isValid() && dayjs(value).isSameOrAfter(dayjs)) {
                    this.dateFuture = dayjs(value);
                } else {
                    console.warn(`Property "future" has an invalid string value "${value}" or is before today`);
                    this.datePast = dayjs().add(2, 'year');
                }
            } else {
                this.dateFuture = dayjs().add(2, 'year');
            }
        }, { immediate: true });
    },
    mounted() {
        this.date = this.sanitizeDate(this.date);
    },
};
</script>

<style lang="scss" scoped>
@import "../../sass/mixins/bg-color";
@import "../../sass/mixins/border-color";
@import "../../sass/mixins/text-color";

.dark {
    .element {
        &.picked {
            @include border-color(datetime-day-border-color-dark, 'colors.gray.400');
            @include bg-color(datetime-picked-bg-color-dark, 'colors.blue.400');
            @include text-color(datetime-picked-text-color-dark, 'colors.white');
        }
        &.range {
            @include bg-color(datetime-range-bg-color-dark, 'colors.blue.400');
            @include text-color(datetime-range-text-color-dark, 'colors.white');
            &.start {
                @include border-color(datetime-day-border-color-dark, 'colors.gray.400');
            }
            &.between {
                @include border-color(datetime-day-border-color-dark, 'colors.gray.400');
            }
            &.end {
                @include border-color(datetime-day-border-color-dark, 'colors.gray.400');
            }
        }

        &:hover:not(.not-possible) {
            @include bg-color(datetime-day-hover-color-dark, 'colors.blue.200');
            @include text-color(datetime-day-hover-text-color-dark, 'colors.black');
        }
        &:hover:not(.range):not(.not-possible) {
            @include border-color(datetime-day-border-color-dark, 'colors.gray.400');
        }
    }
}

.element {
    @apply p-1 cursor-pointer border border-transparent my-1 text-center;
    &.picked {
        @apply rounded-lg border;
        @include border-color(datetime-day-border-color, 'colors.gray.400');
        @include bg-color(datetime-picked-bg-color, 'colors.blue.400');
        @include text-color(datetime-picked-text-color, 'colors.black');

        &.start {
            @apply rounded-none rounded-l-lg;
        }
        &.end {
            @apply rounded-none rounded-r-lg;
        }
    }
    &.today {
        
    }
    &.different-month {
        @apply opacity-75;
    }
    &.not-possible {
        @apply cursor-not-allowed opacity-50;
    }
    &.range {
        @include bg-color(datetime-range-bg-color, 'colors.blue.300');
        @include text-color(datetime-range-text-color, 'colors.black');
        &.start {
            @apply rounded-l-lg border-l border-t border-b border-r-0;
            @include border-color(datetime-day-border-color, 'colors.gray.400');
        }
        &.between {
            @apply rounded-none border-t border-b border-l-0 border-r-0;
            @include border-color(datetime-day-border-color, 'colors.gray.400');
        }
        &.end {
            @apply rounded-r-lg border-r border-t border-b border-l-0;
            @include border-color(datetime-day-border-color, 'colors.gray.400');
        }
    }

    &:hover:not(.not-possible) {
        @include bg-color(datetime-day-hover-color, 'colors.blue.200');
    }
    &:hover:not(.range):not(.not-possible) {
        @apply border rounded-lg;
        @include border-color(datetime-day-border-color, 'colors.gray.400');
    }
}
</style>