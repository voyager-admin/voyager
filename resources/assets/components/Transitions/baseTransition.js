export default {
    props: {
        group: {
            type: Boolean,
            default: false,
        },
        duration: {
            type: [Number, Object],
            default: 300,
        },
        tag: {
            type: String,
            default: 'div'
        },
        class: {
            type: String,
            default: ''
        },
        styles: {
            type: Object,
            default: function () {
                return {
                    animationFillMode: 'both',
                    animationTimingFunction: 'ease-in-out'
                };
            }
        }
    },
    computed: {
        hooks: function () {
            return {
                beforeEnter: this.beforeEnter,
                afterEnter: this.cleanupTransitionStyles,
                beforeLeave: this.beforeLeave,
                leave: this.setAbsolutePositioning,
                afterLeave: this.cleanupTransitionStyles
            };
        },
        enterDuration: function () {
            return this.duration.enter || this.duration;
        },
        leaveDuration: function () {
            return this.duration.leave || this.duration;
        }
    },
    methods: {
        beforeEnter: function (el) {
            el.style.animationDuration = `${this.enterDuration}ms`;

            this.setStyles(el);
        },
        beforeLeave: function (el) {
            el.style.animationDuration = `${this.leaveDuration}ms`;

            this.setStyles(el);
        },
        cleanupTransitionStyles: function (el) {
            Object.keys(this.styles).forEach((key) => {
                if (this.styles[key]) {
                    el.style[key] = '';
                }
            })
            el.style.animationDuration = '';
            el.style.animationDelay = '';
        },
        setStyles: function (el) {
            Object.keys(this.styles).forEach((key) => {
                var val = this.styles[key];
                if (val) {
                    el.style[key] = val;
                }
            });
        },
        setAbsolutePositioning: function (el) {
            if (this.group) {
                el.style.position = 'absolute';
            }
        }
    }
};