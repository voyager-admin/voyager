<template>
    <div>
        <component :is="inline ? 'card' : 'dropdown'" dont-close-on-inside-click dont-show-header ref="dropdown">
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
                    <div class="w-full inline-flex space-x-1" v-if="range">
                        <input type="number" class="input w-full small" :value="padTime(dateFrom.hour())" @input="dateFrom = dateFrom.hour(parseInt($event.target.value))" min="0" max="23">
                        <input type="number" class="input w-full small" :value="padTime(dateFrom.minute())" @input="dateFrom = dateFrom.minute(parseInt($event.target.value))" min="0" max="59">
                        <input type="number" class="input w-full small" :value="padTime(dateFrom.minute())" @input="dateFrom = dateFrom.minute(parseInt($event.target.value))" min="0" max="59" v-if="type.includes('second')">
                    </div>
                    <div class="w-full inline-flex space-x-1" v-else>
                        <input type="number" class="input w-full small" :value="padTime(date.hour())" @input="date = date.hour(parseInt($event.target.value))" min="0" max="23">
                        <input type="number" class="input w-full small" :value="padTime(date.minute())" @input="date = date.minute(parseInt($event.target.value))" min="0" max="59">
                        <input type="number" class="input w-full small" :value="padTime(date.minute())" @input="date = date.minute(parseInt($event.target.value))" min="0" max="59" v-if="type.includes('second')">
                    </div>
                    <div class="w-full inline-flex space-x-1 mt-1" v-if="range">
                        <input type="number" class="input w-full small" :value="padTime(dateTo.hour())" @input="dateTo = dateTo.hour(parseInt($event.target.value))" min="0" max="23">
                        <input type="number" class="input w-full small" :value="padTime(dateTo.minute())" @input="dateTo = dateTo.minute(parseInt($event.target.value))" min="0" max="59">
                        <input type="number" class="input w-full small" :value="padTime(dateTo.minute())" @input="dateTo = dateTo.minute(parseInt($event.target.value))" min="0" max="59" v-if="type.includes('second')">
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
                    <input type="text" class="input w-full mt-2" v-model="textModelFrom" :class="{ error: dateFromInvalid }" />
                    <input type="text" class="input w-full mt-2" v-model="textModelTo" v-if="range" :class="{ error: dateToInvalid }" />
                </div>
            </template>
            <div v-if="inline" class="inline-flex w-full space-x-1">
                <input type="text" class="input w-full mt-2" v-model="textModelFrom" :class="{ error: dateFromInvalid }" />
                <input type="text" v-if="range" class="input w-full mt-2" v-model="textModelTo" :class="{ error: dateToInvalid }" />
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

const FORMATS = {
    //year: 'YYYY',
    //month: 'MM',
    date: 'YYYY-MM-DD',
    date_time: 'YYYY-MM-DD HH:mm',
    date_time_am_pm: 'YYYY-MM-DD hh:mm A',
    date_time_seconds: 'YYYY-MM-DD HH:mm:ss',
    date_time_am_pm_seconds: 'YYYY-MM-DD hh:mm:ss A',
};

export { FORMATS };

export default {
    emits: ['update:modelValue', 'update:from', 'update:to'],
    props: {
        from: {
            type: [String, null],
            default: null,
        },
        to: {
            type: [String, null],
            default: null,
        },
        past: {
            type: [String, Number, null],
            default: null,
        },
        future: {
            type: [String, Number, null],
            default: null,
        },
        distance: {
            type: Number,
            default: 1,
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
    },
    data() {
        return {
            dateFrom: null, // dayjs object generated from "from"
            dateTo: null, // dayjs object generated from "to"
            date: null, // dayjs object generated from "modelValue"
            shownDate: dayjs(), // dayjs object representing the currently shown month and year
            datePast: null, // Dates before this date can not be picked. Generated from prop "past"
            dateFuture: null, // Dates after this date can not be picked. Generated from prop "future"
            range: false, // Is this picker a range-picker? Dynamically set to true when "from" and "to" are valid dates

            dateFromInvalid: false, // Manual entered datetime value valid/invalid
            dateToInvalid: false, // Manual entered datetime value valid/invalid
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
        textModelFrom: {
            get() {
                return this.range ? this.dateFrom.format(this.displayFormat) : this.date.format(this.displayFormat);
            },
            set(value) {
                let date = dayjs(value, this.displayFormat, true);
                if (date.isValid() && this.isDatePossible(date)) {
                    if (this.range) {
                        this.dateFrom = date;
                    } else {
                        this.date = date;
                    }
                    this.dateFromInvalid = false;
                    this.shownDate = date;
                } else {
                    this.dateFromInvalid = true;
                }
            }
        },
        textModelTo: {
            get() {
                return this.range ? this.dateTo.format(this.displayFormat) : '';
            },
            set(value) {
                let date = dayjs(value, this.displayFormat, true);
                if (date.isValid() && this.isDatePossible(date)) {
                    this.dateTo = date;
                    this.dateToInvalid = false;
                    this.shownDate = date;
                } else {
                    this.dateToInvalid = true;
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
        }
    },
    methods: {
        pickDate(date) {
            if (this.isDatePossible(date)) {
                if (this.range) {
                    if (date.isSameOrBefore(this.dateFrom)) {
                        this.dateFrom = date;
                    } else if (date.isSameOrAfter(this.dateTo)) {
                        this.dateTo = date;
                    } else {
                        this.dateFrom = date;
                        this.dateTo = date.add(this.distance, 'day');
                    }
                } else {
                    this.date = date;
                }

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
            return date.isBetween(this.datePast, this.dateFuture, 'day', '[]');
        },
        isMonthPossible(month) {
            return true;
        },
        isYearPossible(year) {
            return true;
        },
        getDateClasses(date) {
            // Date is not possible
            if (!this.isDatePossible(date)) {
                return 'not-possible';
            }

            if (this.range) {
                if (date.isBetween(this.dateFrom, this.dateTo, 'day', '()')) {
                    // Date is in range (exclusive)
                    return 'range between';
                } else if (date.isSame(this.dateFrom, 'day')) {
                    // Date is the lower date in range
                    return 'range start';
                } else if (date.isSame(this.dateTo, 'day')) {
                    // Date is the higher date in range
                    return 'range end';
                }
            } else {
                // Picked date
                if (date.isSame(this.date, 'day')) {
                    return 'picked';
                }
            }

            // Not the current month
            if (!date.isBetween(dayjs(this.shownDate.date(0)), dayjs(this.shownDate.date(0).add(this.shownDate.daysInMonth()+1, 'day')), 'day', '()')) {
                return 'different-month';
            }

            // Today
            if (date.isSame(dayjs, 'day')) {
                return 'today';
            }
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
            this.range = false;
            let date = dayjs(value || undefined);

            if (date.isValid()) {
                this.date = this.sanitizeDate(date);
                this.shownDate = date;
            }
        }, { immediate: true });

        // Watch incoming "from"
        this.$watch(() => this.from, (value, old) => {
            let date = dayjs(value || undefined);

            if (value !== null && date.isValid()) {
                this.dateFrom = this.sanitizeDate(date);
                this.shownDate = date;
                this.range = (this.dateTo && this.dateTo.isValid()); // Dynamically set range if "dateFrom" and "dateTo" are valid dates
            }
        }, { immediate: true });

        // Watch incoming "to"
        this.$watch(() => this.to, (value, old) => {
            let date = dayjs(value || undefined);

            if (value !== null && date.isValid()) {
                this.dateTo = this.sanitizeDate(date);
                this.shownDate = date;
                this.range = (this.dateFrom && this.dateFrom.isValid()); // Dynamically set range if "dateFrom" and "dateTo" are valid dates
            }
        }, { immediate: true });

        // Watch internal "date"
        this.$watch(() => this.date, (value, old) => {
            value !== old && this.$emit('update:modelValue', value.tz(dayjs.tz.guess()).toISOString());
        }, { deep: true });

        // Watch internal "dateFrom"
        this.$watch(() => this.dateFrom, (value, old) => {
            value !== old && this.$emit('update:from', value.tz(dayjs.tz.guess()).toISOString());
        }, { deep: true });

        // Watch internal "dateTo"
        this.$watch(() => this.dateTo, (value, old) => {
            value !== old && this.$emit('update:to', value.tz(dayjs.tz.guess()).toISOString());
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
        if (this.range) {
            this.dateFrom = this.sanitizeDate(this.dateFrom);
            this.dateTo = this.sanitizeDate(this.dateTo);
        } else {
            this.date = this.sanitizeDate(this.date);
        }
    }
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