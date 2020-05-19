<template>
    <div v-if="width < 100" class="track horizontal" :class="['h-'+size]" ref="container" @click="jump">
        <div
            class="handle"
            :class="[dragging || draggingFromParent ? '' : 'transition', 'h-'+size]"
            ref="scrollbar"
            @touchstart="startDrag"
            @mousedown="startDrag"
            :style="{ width: width+'%', left: scrolling + '%' }">
        </div>
    </div>
</template>

<script>
export default {
    props: {
        draggingFromParent: Boolean,
        scrolling: Number,
        wrapper: Object,
        area: Object,
        onChangePosition: Function,
        onDragging: Function,
        onStopDrag: Function,
        size: Number,
    },
    data: function () {
        return  {
            width: 0,
            dragging: false,
            start: 0,
        }
    },
    watch: {
        'wrapper.width': function () {
            this.calculateSize();
        },
        'area.width': function () {
            this.calculateSize();
        },
    },
    methods: {
        startDrag: function (e) {
            e.preventDefault();
            e.stopPropagation();
            e = e.changedTouches ? e.changedTouches[0] : e;
            this.dragging = true;
            this.start = e.clientX;
        },
        onDrag: function (e) {
            if (this.dragging) {
                this.onDragging();
                e.preventDefault();
                e.stopPropagation();
                e = e.changedTouches ? e.changedTouches[0] : e;
                let xMovement = e.clientX - this.start;
                let xMovementPercentage = xMovement / this.wrapper.width * 100;
                this.start = e.clientX;
                let next = this.scrolling + xMovementPercentage;
                this.onChangePosition(next, 'horizontal');
            }
        },
        stopDrag: function (e) {
            if (this.dragging) {
                this.onStopDrag();
                this.dragging = false;
            }
        },
        jump: function (e) {
            if (e.target === this.$refs.container) {
                let position = this.$refs.scrollbar.getBoundingClientRect();
                let xMovement = e.clientX - position.left;
                let centerize = (this.width / 2);
                let xMovementPercentage = xMovement / this.wrapper.width * 100 - centerize;
                this.start = e.clientX;
                let next = this.scrolling + xMovementPercentage;
                this.onChangePosition(next, 'horizontal');
            }
        },
        calculateSize: function () {
            this.width = this.wrapper.width / this.area.width * 100;
        },
    },
    mounted: function () {
        this.calculateSize();
        document.addEventListener('mousemove', this.onDrag);
        document.addEventListener('touchmove', this.onDrag);
        document.addEventListener('mouseup', this.stopDrag);
        document.addEventListener('touchend', this.stopDrag);
    },
    beforeDestroy: function () {
        document.removeEventListener('mousemove', this.onDrag);
        document.removeEventListener('touchmove', this.onDrag);
        document.removeEventListener('mouseup', this.stopDrag);
        document.removeEventListener('touchend', this.stopDrag);
    }
}
</script>