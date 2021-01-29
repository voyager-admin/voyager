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
            default() {
                return {
                    animationFillMode: 'both',
                    animationTimingFunction: 'ease-in-out'
                };
            }
        }
    },
    computed: {
        hooks() {
            return {
                beforeEnter: this.beforeEnter,
                afterEnter: this.cleanupTransitionStyles,
                beforeLeave: this.beforeLeave,
                leave: this.setAbsolutePositioning,
                afterLeave: this.cleanupTransitionStyles
            };
        },
        enterDuration() {
            return this.duration.enter || this.duration;
        },
        leaveDuration() {
            return this.duration.leave || this.duration;
        }
    },
    methods: {
        beforeEnter(el) {
            el.style.animationDuration = `${this.enterDuration}ms`;
            this.setStyles(el);
        },
        beforeLeave(el) {
            el.style.animationDuration = `${this.leaveDuration}ms`;
            this.setStyles(el);
        },
        cleanupTransitionStyles(el) {
            Object.keys(this.styles).forEach((key) => {
                if (this.styles[key]) {
                    el.style[key] = '';
                }
            })
            el.style.animationDuration = '';
            el.style.animationDelay = '';
        },
        setStyles(el) {
            Object.keys(this.styles).forEach((key) => {
                var val = this.styles[key];
                if (val) {
                    el.style[key] = val;
                }
            });
        },
        setAbsolutePositioning(el) {
            if (this.group) {
                el.style.position = 'absolute';
            }
        }
    }
};